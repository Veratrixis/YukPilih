import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { Head } from '@inertiajs/react';
import PollCard from '../Components/PollCard';

export default function PollsPage() {
  const [polls, setPolls] = useState([]);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchPolls();
  }, []);

  const fetchPolls = async () => {
    try {
      const accessToken = sessionStorage.getItem('access_token');
      const response = await axios.get('/api/poll', {
        headers: { Authorization: `Bearer ${accessToken}` },
      });
      setPolls(response.data.data);
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
      await axios.post(
        '/api/auth/logout',
        {},
        {
          headers: { Authorization: `Bearer ${accessToken}` },
        }
      );
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

  const handleProfile = () => {
    window.location.replace('/profile');
  };
  
  const handlePolls = () => {
    window.location.replace('/create_poll');
  };

  return (
    <div>
      <Head title="Create Polls" />

      <nav>
        <button onClick={handleLogout}>Logout</button>
        <button onClick={handlePolls}>Add Polls</button>
        <button onClick={handleProfile}>Profile</button>
      </nav>
      <div className="pollsContainer">
        {polls.map((poll) => (
          <PollCard key={poll.id} pollData={poll} onDelete={fetchPolls} />
        ))}
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
