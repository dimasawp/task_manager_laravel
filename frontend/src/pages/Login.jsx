import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { loginApi } from "../services/auth";
import { useAuth } from "../hooks/useAuth";
import Button from "../components/Button";
import FormInput from "../components/FormInput";

export default function Login() {
    const [username, setUsername] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");
    const navigate = useNavigate();
    const { login } = useAuth();

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const res = await loginApi(username, password);
            login(res.user, res.token);
            navigate("/");
        } catch (err) {
            setError("Username atau password salah");
        }
    };

    return (
        <div>
            <h1 className="text-2xl font-semibold mb-6 text-center">Login</h1>
            {error && <p className="text-sm text-red-500 mb-4">{error}</p>}
            <form onSubmit={handleSubmit}>
                <FormInput
                    label="Username"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                    placeholder="Masukkan username"
                />
                <FormInput
                    label="Password"
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="Masukkan password"
                />
                <Button type="submit" color="blue" className="w-full mt-4">
                    Masuk
                </Button>
            </form>
        </div>
    );
}
