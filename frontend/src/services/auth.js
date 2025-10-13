import axios from "axios";

const API_URL = import.meta.env.VITE_API_URL;

export const loginApi = async (username, password) => {
    const response = await axios.post(`$API_URL/login`, { username, password });
    return response.data;
};

export const registerApi = async (data) => {
    const response = await axios.post(`${API_URL}/register`, data);
    return response.data;
};
