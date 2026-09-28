FROM oven/bun:1.3.3-alpine
WORKDIR /app
COPY package.json server.ts ./
EXPOSE 9117
CMD ["bun", "run", "server.ts"]
