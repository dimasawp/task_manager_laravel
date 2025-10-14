# --- Frontend (React + Vite) Development Dockerfile ---

# Pakai Node versi terbaru untuk Vite
FROM node:22-alpine

# Set working directory
WORKDIR /app

# Salin file package.json & package-lock.json dulu
COPY package*.json ./

# Install dependencies
RUN npm install

# Salin seluruh project
COPY . .

# Jalankan development server Vite
CMD ["npm", "run", "dev", "--", "--host"]
