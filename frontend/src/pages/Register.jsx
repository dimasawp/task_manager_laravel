import { useState } from "react";
import { Link } from "react-router-dom";
import { useAuth } from "../hooks/useAuth";
import Button from "../components/Button";
import FormInput from "../components/FormInput";

export default function Register() {
    const [name, setName] = useState("");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const [error, setError] = useState("");
    const { register } = useAuth();

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (password !== confirmPassword) {
            setError("Password dan konfirmasi password tidak sesuai");
            return;
        }

        try {
            await register({ name, email, password });
        } catch (err) {
            setError("Gagal mendaftar, coba lagi.");
        }
    };

    return (
        <div>
            <h1 className="text-2xl font-semibold mb-6 text-center">Register</h1>
            {error && <p className="text-sm text-red-500 mb-4">{error}</p>}
            <form onSubmit={handleSubmit} className="mb-4">
                <FormInput
                    label="Name"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="Masukkan nama"
                />
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
            <p className="text-center">
                Have an account?{" "}
                <Link to="/login" className="text-blue-500 underline underline-offset-1">
                    Login
                </Link>
            </p>
        </div>
    );
}
