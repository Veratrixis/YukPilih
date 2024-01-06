import React, { useState, useEffect } from 'react';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

export default function ProfilePage() {
    const [userData, setUserData] = useState(null);
    const [error, setError] = useState(null);
  
    useEffect(() => {
      fetchUserProfile();
    }, []);
  
    const fetchUserProfile = async () => {
      try {
        const accessToken = sessionStorage.getItem('access_token');
  
        const response = await axios.get('/api/auth/me', {
          headers: { Authorization: `Bearer ${accessToken}` },
        });
        setUserData(response.data.data);
      } catch (error) {
        setError({
            status: error.response.status ? error.response.status : null,
            message: error.response.data.message ? error.response.data.message : 'Something went wrong!',
        });
      }
    };
  
    const handleLogout = async () => {
      try {
        const accessToken = sessionStorage.getItem('access_token');
  
        await axios.post('/api/auth/logout', {}, {
          headers: { Authorization: `Bearer ${accessToken}` },
        });

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

    const handleCancel = () => {
      window.location.replace('/polls');
    };

    return (
      <div>
        <nav>
          <button onClick={handleCancel}>Cancel</button>
        </nav>
        <div className='pollContainer'>
          <div className='title'>My Profile</div>
            {userData && (
              <div className='myData'>
                <div>ID: <span>{userData.id}</span></div>
                <div>Username: <span>{userData.username}</span></div>
                <div>Role: <span>{userData.role}</span></div>
                <div>Division ID: <span>{userData.division_id}</span></div>
                {/* <div>Division: <span>{userData.division}</span></div> */}
                <div>Created At: <span>{userData.created_at}</span></div>
                <div>Updated At: <span>{userData.updated_at}</span></div>
                <button onClick={handleLogout}>Logout</button>
              </div>
            )}
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
  };
  
