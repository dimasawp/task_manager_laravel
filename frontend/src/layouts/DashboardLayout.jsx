import SideBar from "../components/Sidebar";
import Navbar from "../components/Navbar";

export default function DashboardLayout({ children }) {
    return (
        <div className="flex min-h-screen bg-gray-50">
            <SideBar />
            <div className="flex flex-col flex-1">
                <Navbar />
                <main className="p-6">{children}</main>
            </div>
        </div>
    );
}
