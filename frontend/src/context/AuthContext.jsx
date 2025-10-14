import { createContext, useState, useEffect } from "react";
import { loginApi, registerApi, logoutApi, meApi } from "../services/auth";
import { useNavigate } from "react-router-dom";

export const AuthContext = createContext();

export default function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const navigate = useNavigate();

    useEffect(() => {
        const token = localStorage.getItem("token");
        if (token) {
            meApi()
                .then((res) => setUser(res))
                .catch(() => logout());
        }
    }, []);

    const login = async (email, password) => {
        const res = await loginApi(email, password);
        localStorage.setItem("token", res.access_token);
        const userData = await meApi();
        setUser(userData);
        navigate("/tasks");
    };

    const register = async ({ name, email, password }) => {
        const res = await registerApi({ name, email, password });
        localStorage.setItem("token", res.access_token);
        const userData = await meApi();
        setUser(userData);
        navigate("/tasks");
    };

    const logout = async () => {
        try {
            await logoutApi();
        } catch (error) {
            console.error("Logout failed:", error);
        }
        localStorage.removeItem("token");
        setUser(null);
        navigate("/login");
    };

    return <AuthContext.Provider value={{ user, login, register, logout }}>{children}</AuthContext.Provider>;
}
