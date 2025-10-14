import { useState } from "react";
import { Link } from "react-router-dom";
import { useAuth } from "../hooks/useAuth";
import Button from "../components/Button";
import FormInput from "../components/FormInput";

export default function Login() {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");
    const { login } = useAuth();

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await login(email, password);
        } catch (err) {
            setError("email atau password salah");
        }
    };

    return (
        <div>
            <h1 className="text-2xl font-semibold mb-6 text-center">Login</h1>
            {error && <p className="text-sm text-red-500 mb-4">{error}</p>}
            <form onSubmit={handleSubmit} className="mb-4">
                <FormInput
                    label="Email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="Masukkan email"
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
            <p className="text-center">
                Don't have an account?{" "}
                <Link to="/register" className="text-blue-500 underline underline-offset-1">
                    Register
                </Link>
            </p>
        </div>
    );
}
