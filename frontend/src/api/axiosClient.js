import axios from "axios"

// bila import dan pakai axiosClient nanti dicek ada token tidak kalau tidak berarti request tanpa token jadinya invalid
const axiosClient = axios.create({
    baseURL: import.meta.env.Vite_APP_BASE_URL,
})

axiosClient.interceptors.request.use((config) => {
    const token = localStorage.getItem("token")
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

export default axiosClient
