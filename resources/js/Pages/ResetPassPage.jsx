import { useState } from 'react';
import axios from 'axios';
import { Head } from '@inertiajs/react';

export default function ResetPassPage() {
    const [currentPassword, setCurrentPassword] = useState('');
    const [newPassword, setNewPassword] = useState('');
    const [error, setError] = useState(null);

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const accessToken = sessionStorage.getItem('access_token');
            await axios.post(
                '/api/auth/reset_password',
                { old_password: currentPassword, new_password: newPassword },
                {
                    headers: {
                        Authorization: `Bearer ${accessToken}`,
                    },
                }
            );

            sessionStorage.removeItem('access_token');
            window.location.replace('/');
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
            <Head title="Reset Password" />

            <div className="content">
                <form onSubmit={handleSubmit}>
                    <div className = "authContainer">
                        <label className="title">Reset Password</label>
                        <div>
                            <label>Current Password:</label>
                            <div>
                                <input
                                    type="password"
                                    value={currentPassword}
                                    onChange={(e) => setCurrentPassword(e.target.value)}
                                />
                            </div>
                        </div>
                        <div>
                            <label>New Password:</label>
                            <div>
                                <input
                                    type="password"
                                    value={newPassword}
                                    onChange={(e) => setNewPassword(e.target.value)}
                                />
                            </div>
                        </div>
                        <div>
                            <button type="submit">Reset Password</button>
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
