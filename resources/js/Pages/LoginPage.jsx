import { useState } from 'react';
import axios from 'axios';
import { Head } from '@inertiajs/react';

export default function LoginPage() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState(null);

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const response = await axios.post('/api/auth/login', { username, password });
            sessionStorage.setItem('access_token', response.data.access_token);

            if (response.data.default_password) {
                window.location.href = '/reset_password';
            } else {
                window.location.href = '/polls';
            }
        } catch (error) {
            setError({
                status: error.response.status ? error.response.status : null,
                message: error.response.data.message ? error.response.data.message : 'Something went wrong!',
            });
        }
    };

    const handleCloseErrorModal = () => {
        setError(null);
    };

    return (
        <div>
            <Head title="Login App" />

            <div className="content">
                <form onSubmit={handleSubmit}>
                    <div className = "authContainer">
                        <label className="title">Login YukPilih</label>
                        <div>
                            <label>Username:</label>
                            <div>
                                <input
                                    type="text"
                                    value={username}
                                    onChange={(e) => setUsername(e.target.value)}
                                />
                            </div>
                        </div>
                        <div>
                            <label>Password:</label>
                            <div>
                                <input
                                    type="password"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                />
                            </div>
                        </div>
                        <div>
                            <button type="submit">Login</button>
                        </div>
                    </div>
                </form>
            </div>

            {error && (
                <div className="errorModal">
                <div className="modalContent">
                    <div className="modalHeader">
                    <span>{`Error ${error.status}`}</span>
                    </div>
                    <div>
                    <div>{`${error.message}`}</div>
                    <button className="modalClose" onClick={handleCloseErrorModal}>
                        &times;
                    </button>
                    </div>
                </div>
                </div>
            )}
        </div>
    );
}
