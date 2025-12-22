import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
    CallToolRequestSchema,
    ListToolsRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";
import mysql from "mysql2/promise";

// Database configuration
const dbConfig = {
    host: process.env.DB_HOST || "localhost",
    user: process.env.DB_USER || "root",
    password: process.env.DB_PASSWORD || "",
    database: process.env.DB_NAME || "aiqz",
};

// Create the MCP server
const server = new Server(
    {
        name: "aiquiz-db-server",
        version: "1.0.0",
    },
    {
        capabilities: {
            tools: {},
        },
    }
);

let connection;

async function getDbConnection() {
    if (!connection) {
        connection = await mysql.createConnection(dbConfig);
    }
    return connection;
}

// Register tools
server.setRequestHandler(ListToolsRequestSchema, async () => {
    return {
        tools: [
            {
                name: "execute_sql",
                description: "Run a SELECT, INSERT, UPDATE, or DELETE query on the 'aiqz' database.",
                inputSchema: {
                    type: "object",
                    properties: {
                        sql: {
                            type: "string",
                            description: "The full SQL query to execute",
                        },
                    },
                    required: ["sql"],
                },
            },
            {
                name: "list_tables",
                description: "List all tables in the database.",
                inputSchema: {
                    type: "object",
                    properties: {},
                },
            },
            {
                name: "describe_table",
                description: "Get the column definitions for a specific table.",
                inputSchema: {
                    type: "object",
                    properties: {
                        table: {
                            type: "string",
                            description: "The name of the table to describe",
                        },
                    },
                    required: ["table"],
                },
            },
        ],
    };
});

// Handle tool calls
server.setRequestHandler(CallToolRequestSchema, async (request) => {
    const { name, arguments: args } = request.params;
    const conn = await getDbConnection();

    try {
        if (name === "execute_sql") {
            const [rows] = await conn.execute(args.sql);
            return {
                content: [{ type: "text", text: JSON.stringify(rows, null, 2) }],
            };
        }

        if (name === "list_tables") {
            const [rows] = await conn.execute("SHOW TABLES");
            const tables = rows.map(row => Object.values(row)[0]);
            return {
                content: [{ type: "text", text: `Tables in aiqz:\n- ${tables.join("\n- ")}` }],
            };
        }

        if (name === "describe_table") {
            const [rows] = await conn.execute(`DESCRIBE ${args.table}`);
            return {
                content: [{ type: "text", text: JSON.stringify(rows, null, 2) }],
            };
        }

        throw new Error(`Unknown tool: ${name}`);
    } catch (error) {
        return {
            content: [{ type: "text", text: `Error: ${error.message}` }],
            isError: true,
        };
    }
});

// Start the server using stdio transport
const transport = new StdioServerTransport();
await server.connect(transport);
console.error("AI Quiz Database MCP server running on stdio");
