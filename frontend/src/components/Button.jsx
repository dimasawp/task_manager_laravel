export default function Button({ children, type = "button", className = "", ...props }) {
    return (
        <button
            type={type}
            className={`px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition ${className}`}
            {...props}
        >
            {children}
        </button>
    );
}
