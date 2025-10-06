# ./frontend/Dockerfile
FROM node:14

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy all files
COPY . .

# Build the app
# RUN npm run build

# Expose port 8080
EXPOSE 8080

# Force Vue to run on port 8086
ENV PORT=8080
# Enable polling for changes inside Docker
ENV CHOKIDAR_USEPOLLING=true 
# Start the app
CMD ["npm", "run", "serve"]