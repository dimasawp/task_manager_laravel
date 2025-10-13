export default function Navbar() {
    return (
        <header className="bg-white shadow-sm p-4 flex justify-between items-center">
            <h1 className="text-lg font-semibold">Dashboard</h1>
            <button className="text-sm text-gray-600 hover:text-red-600">Logout</button>
        </header>
    );
}
