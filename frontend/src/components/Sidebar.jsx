import { Link } from "react-router-dom";

export default function Sidebar() {
    return (
        <aside className="w-64 bg-white shadow-md p-4 hidden md:block">
            <h2 className="text-xl font-bold mb-4">Task Manager</h2>
            <nav className="flex flex-col space-y-2">
                <Link to="/" className="text-gray-700 hover:text-blue-600">
                    Dashboard
                </Link>
                <Link to="/projects" className="text-gray-700 hover:text-blue-600">
                    Projects
                </Link>
                <Link to="/tasks" className="text-gray-700 hover:text-blue-600">
                    Tasks
                </Link>
            </nav>
        </aside>
    );
}
