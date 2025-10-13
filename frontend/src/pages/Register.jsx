import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { registerApi } from "../services/auth";
import Button from "../components/Button";
import FormInput from "../components/FormInput";

export default function Login() {
    const [username, setUsername] = useState("");
    const [password, setPassword] = useState("");
    const [confirmPassword, setconfirmPassword] = useState("");
    const [error, setError] = useState("");
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (password !== confirmPassword) {
            setError("Password dan konfirmasi password tidak sesuai");
        }

        try {
            await registerApi(username, password);
            navigate("/login");
        } catch (err) {
            setError("Gagal mendaftar, coba lagi.");
        }
    };

    return (
        <div>
            <h1 className="text-2xl font-semibold mb-6 text-center">Register</h1>
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
                <FormInput
                    label="Konfirmasi Password"
                    type="password"
                    value={confirmPassword}
                    onChange={(e) => setConfirmPassword(e.target.value)}
                    placeholder="Masukkan ulang password"
                />
                <Button type="submit" color="blue" className="w-full mt-4">
                    Daftar
                </Button>
            </form>
        </div>
    );
}
