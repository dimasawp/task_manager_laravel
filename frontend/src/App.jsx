import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import AuthLayout from "./layouts/AuthLayout";
import DashboardLayout from "./layouts/DashboardLayout";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Dashboard from "./pages/Dashboard.jsx";
import PrivateRoute from "./components/PrivateReoute";
import "./App.css";

function App() {
    return (
        // <Router>
        <Routes>
            {/* public routes */}
            <Route
                path="/login"
                element={
                    <AuthLayout>
                        <Login />
                    </AuthLayout>
                }
            />
            <Route
                path="/register"
                element={
                    <AuthLayout>
                        <Register />
                    </AuthLayout>
                }
            />

            {/* protected routes */}
            <Route element={<PrivateRoute />}>
                <Route
                    path="/tasks"
                    element={
                        <DashboardLayout>
                            <Dashboard />
                        </DashboardLayout>
                    }
                />
            </Route>
        </Routes>
        // </Router>
    );
}

export default App;
