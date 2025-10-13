export default function AuthLayout({ children }) {
    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-100">
            <div className="w-96 mx-w-md bg-white rounded-2xl shadow-lg p-6">{children}</div>
        </div>
    );
}
