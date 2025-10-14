import api from "./api";

const API_URL = import.meta.env.VITE_API_URL;

export const loginApi = async (email, password) => {
    const response = await api.post("/login", { email, password });
    return response.data;
};

export const registerApi = async (data) => {
    const response = await api.post("/register", data);
    return response.data;
};

export const logoutApi = async () => {
    const response = await api.post("/logout");
    return response.data;
};

export const meApi = async () => {
    const response = await api.get("/me");
    return response.data;
};
