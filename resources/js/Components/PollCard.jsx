import React, { useState } from 'react';
import axios from 'axios';

export default function PollCard({ pollData, onDelete }) {
  const [selectedChoice, setSelectedChoice] = useState(null);
  const [error, setError] = useState(null);

  const handleVote = async () => {
    try {
      if (selectedChoice !== null) {
        const accessToken = sessionStorage.getItem('access_token');
        await axios.post(
          `/api/poll/${pollData.id}/vote/${selectedChoice}`,
          {},
          {
            headers: { Authorization: `Bearer ${accessToken}` },
          }
        );
      } else {
        setError({
            status: 'Data',
            message: 'Please select a choice before voting!',
        });
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

  const renderChoices = () => {
    const totalPoint = pollData.results.reduce((acc, result) => acc + result.point, 0);
  
    return pollData.choices.map((choice, index) => (
      <div key={choice.id}>
        <input
          type="radio"
          id={choice.id}
          name="poll-choice"
          value={choice.choice}
          onChange={() => setSelectedChoice(choice.id)}
        />
        <label className='labelChoice' htmlFor={choice.id}>{choice.choice}</label>
        {pollData.results && pollData.results[index] && totalPoint !== 0 && (
          <div className="scoreLoader">
            <div
              className="scoreProgress"
              style={{
                width: `${
                  (pollData.results[index].point / totalPoint) * 100 + '%' 
                }`,
              }}
            >${((pollData.results[index].point / totalPoint) * 100).toFixed(2) + '%'}</div>
          </div>
        )}
        {pollData.results && pollData.results[index] && totalPoint === 0 && (
          <div className="scoreLoader">
            <div className="scoreProgress" style={{ width: '0%' }}>0%</div>
          </div>
        )}
      </div>
    ));
  };  

  const handleDelete = async () => {
    try {
      const accessToken = sessionStorage.getItem('access_token');
      await axios.delete(`/api/poll/${pollData.id}`, {
        headers: { Authorization: `Bearer ${accessToken}` },
      });
      onDelete();
    } catch (error) {
      setError({
          status: error.response.status ? error.response.status : null,
          message: error.response.data.message ? error.response.data.message : 'Something went wrong!',
      });
    }
  };

  return (
    <div className="pollCard">
      <button className="deleteButton" onClick={handleDelete}>
        Delete
      </button>
      <div className='titleText'>{pollData.title}</div>
      <span className='subText'>created by: {pollData.creator} | deadline: {pollData.deadline}</span>
      <div className='descriptionText'>{pollData.description}</div>
      <form className='choiceContainer' onSubmit={(e) => { e.preventDefault(); handleVote(); }}>
        {renderChoices()}
        <button className="submitButton" type="submit">Vote</button>
      </form>

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
