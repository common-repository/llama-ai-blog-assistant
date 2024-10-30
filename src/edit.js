import { useState } from "@wordpress/element";
import { TextareaControl, Button } from "@wordpress/components";
import { Spinner } from "@wordpress/components";
import { Icon, close as closeIcon } from "@wordpress/icons";
import { useEntityProp } from "@wordpress/core-data";
import { useSelect } from "@wordpress/data";
import {
	useBlockProps,
	store as blockEditorStore,
} from "@wordpress/block-editor";
import { OPEN_AI_API_URL, CONTACT_SUPPORT_URL } from "./config";

import { Panel, PanelBody, PanelRow } from "@wordpress/components";
import { more } from "@wordpress/icons";
const tokenCalculator = ({ aiResponse }) => {
	// 750 words = 1000 tokens
	// 1 word = 1.33 tokens
	const words = aiResponse.split(" ").length;
	const tokens = Math.round(words * 1.33);
	return tokens;
}
export default function Edit({ attributes, setAttributes }) {
	const { content } = attributes;
	const [prompt, setPrompt] = useState("");
	const [isLoading, setIsLoading] = useState(false);
	const [tokenUsed, setTokenUsed] = useState(0);
	const [timeTaken, setTimeTaken] = useState(0);
	const blockProps = useBlockProps();
	const [meta, setMeta] = useEntityProp("postType", "post", "meta");

	const existingTokenUsed = meta.llama_token_used || 0;
	const existingTimeTaken = meta.llama_time_taken || 0;

	const { getCurrentPost } = useSelect((select) => select("core/editor"));
	const postId = getCurrentPost().id;

	const getSelectedBlockId = () => {
		const selectedBlock = wp.data
			.select("core/block-editor")
			.getSelectedBlock();
		return selectedBlock ? selectedBlock.clientId : null;
	};

	const { postTitle, postContent } = useSelect((select) => {
		return {
			postTitle: select("core/editor").getEditedPostAttribute("title"),
			postContent: select("core/editor")
				.getEditedPostContent()
				.replace(/<\!--.*?-->/g, "")
				.replace(/<[^>]*>/g, ""),
		};
	}, []);

	const fetchSettings = async () => {
		try {
			const result = await fetch(
				"/wp-json/llama-ai-blog-assistant/v1/get-settings"
			);

			const data = await result.json();
			return data;
		} catch (error) {
			console.error(error);
			return {};
		}
	};

	const fetchResponseFromOpenAI = async () => {
		try {
			if (prompt === "") {
				wp.data
					.dispatch("core/notices")
					.createNotice("error", "Prompt cannot be empty", {
						isDismissible: true,
					});
				return;
			}
			setIsLoading(true);
			const settings = await fetchSettings();
			const start = new Date().getTime();

			const systemMessage = `
			You are an AI assistant who is helping me write a blog. you will give response only in plain text. Also proprely add new line( use <br />) and new paragraphs(use <br /><br />).
			I will ask you a question that you will answer based on the following blog draft that has been written so far:

			blog title: ${postTitle}

			blog content: ${postContent}

			If the blog title or blog content is empty then give a reasonable answer.
			`;
			const response = await fetch(OPEN_AI_API_URL,
				{
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						Authorization: "Bearer " + settings.apiKey,
					},
					body: JSON.stringify({
						model: settings.model,
						stream: true,
						messages: [
							{
								role: "system",
								content: systemMessage,
							},
							{
								role: "user",
								content: prompt,
							},
						],
					}),
				}
			);
			// Create a readable stream from the response body
			const reader = response.body.getReader();
			let aiResponse = "";
			let clientId = "";
			const newBlock = wp.blocks.createBlock("core/paragraph", {
				content: aiResponse,
			});
			clientId = newBlock.clientId;
			wp.data
				.dispatch("core/block-editor")
				.insertBlocks(
					newBlock,
					wp.data.select("core/block-editor").getBlockInsertionPoint().index - 1
				);
			// Define a function to process each chunk of data from the stream
			const processChunk = async ({ done, value }) => {
				if (done) {
					console.log("Stream complete");
					const end = new Date().getTime();
					const timeTaken = Math.round((end - start) / 1000);

					const totalTokenUsed =
						existingTokenUsed + tokenCalculator({ aiResponse });
					const totalTimeTaken = existingTimeTaken + timeTaken;

					setMeta({
						...meta,
						llama_token_used: totalTokenUsed,
						llama_time_taken: totalTimeTaken,
					});

					setTokenUsed(tokenUsed);
					setTimeTaken(timeTaken);
					setIsLoading(false);
					// const blocks = wp.blocks.parse(aiResponse);
					// wp.data.dispatch("core/block-editor").removeBlock(clientId);
					// wp.data
					// 	.dispatch("core/block-editor")
					// 	.insertBlocks(
					// 		blocks,
					// 		wp.data.select("core/block-editor").getBlockInsertionPoint()
					// 			.index - 1
					// 	);
					return;
				}

				// Process the chunk of data
				const chunks = new TextDecoder().decode(value).split("data: ");

				chunks.forEach((chunk) => {
					if (!chunk || chunk.includes("[DONE]")) return;
					try {
						const jsonData = JSON.parse(chunk);
						const message = jsonData.choices[0].delta.content;
						if (message) {
							aiResponse += message;
							wp.data.dispatch("core/block-editor").updateBlock(clientId, {
								attributes: { content: aiResponse },
							});
						}
					} catch (error) {
						console.log(chunk);
						console.error(error);
					}
				});

				// Continue reading the next chunk
				reader.read().then(processChunk);
			};
			// Start reading the stream
			return processChunk(await reader.read());
		} catch (error) {
			console.error(error);
			// Show error message in a toast, as per the WordPress convention.
			wp.data
				.dispatch("core/notices")
				.createNotice(
					"error",
					"An error occurred while fetching data from OpenAI: " +
					error.message +
					" " +
					error.code,
					{
						isDismissible: true,
					}
				);
			setIsLoading(false);
		}
	};

	return (
		<div
			{...blockProps}
			style={{
				paddingTop: 0,
			}}
		>
			<div
				style={{
					display: "flex",
					justifyContent: "flex-end",
					width: "100%",
				}}
			>
				<Icon
					style={{
						paddingTop: "20px",
						paddingBottom: "20px",
						cursor: "pointer",
					}}
					icon={closeIcon}
					onClick={() => {
						wp.data.dispatch("core/editor").removeBlock(getSelectedBlockId());
					}}
				/>
			</div>
			<Panel
				// using label instead of title to avoid showing the huge text in the block
				// in normal wordpress editor heading goues out of the block.
				header={<label style={{ fontSize: "1rem" }}>Llama: ChatGPT-powered Blog Assistant</label>}
			>
				<div
					style={{
						padding: "10px",
					}}
				>
					<TextareaControl
						label="Send a prompt to Llama"
						value={prompt}
						onChange={(value) => setPrompt(value)}
					/>

					<Button
						variant={isLoading ? "tertiary" : "primary"}
						onClick={fetchResponseFromOpenAI}
						disabled={isLoading}
					>
						{isLoading ? "Sending..." : "Send"}
					</Button>
					{isLoading && <Spinner />}

					{tokenUsed != 0 ? (
						<p>
							Token Used: {tokenUsed}
							<br />
							Time Taken: {timeTaken} seconds
						</p>
					) : (
						<p>
							Llama, your ChatGPT-powered blogging assistant, will provide a
							response in an editable block. After using Llama, you can delete
							the Llama block from your post.
						</p>
					)}
				</div>
				<PanelBody title="Premium" icon={more} initialOpen={false}>
					<PanelRow>
						<p>
							Skyrocket your content's relevance and accuracy with Llama Premium. Adopt your brand's unique tone, and style to craft content that mirrors your voice. Bolster your articles with relevant data and research. Click to upgrade to Llama Premium!
						</p>
						<ul>
							<li>
								Benefit: Tailor your content to match your brand's unique voice.
							</li>
							<li>
								Benefit: Add more credibility to your content with custom data.
							</li>
						</ul>
					</PanelRow>
					<PanelRow>
						<Button
							isSecondary
							href="https://marketingllama.ai/pricing/"
							target="_blank"
						>
							Upgrade to Premium
						</Button>
					</PanelRow>
				</PanelBody>
			</Panel>
		</div>
	);
}
