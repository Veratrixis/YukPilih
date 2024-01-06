import React, { useState } from 'react';
import axios from 'axios';
import { Head } from '@inertiajs/react';

export default function CreatePollPage() {
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [deadline, setDeadline] = useState('');
  const [choices, setChoices] = useState(['', '']);
  const [error, setError] = useState(null);

  const handleAddChoice = () => {
    setChoices([...choices, '']);
  };

  const handleRemoveChoice = (index) => {
    const updatedChoices = [...choices];
    updatedChoices.splice(index, 1);
    setChoices(updatedChoices);
  };

  const handleChoiceChange = (index, value) => {
    const updatedChoices = [...choices];
    updatedChoices[index] = value;
    setChoices(updatedChoices);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const accessToken = sessionStorage.getItem('access_token');

      const response = await axios.post(
        '/api/poll',
        {
          title,
          description,
          deadline,
          choices,
        },
        {
          headers: { Authorization: `Bearer ${accessToken}` },
        }
      );

      console.log('Poll created successfully:', response.data);
      window.location.href = '/polls';
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
      <Head title="Create Polls" />

      <nav>
        <button onClick={handleCancel}>Cancel</button>
      </nav>
      <div className='pollContainer'>
        <div className='title'>Create Poll</div>
        <form onSubmit={handleSubmit}>
          <div>
            <label>Title:</label>
            <input
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
            />
          </div>
          <div>
            <label>Description:</label>
            <textarea
              value={description}
              onChange={(e) => setDescription(e.target.value)}
            ></textarea>
          </div>
          <div>
            <label>Deadline:</label>
            <input
              type="datetime-local"
              value={deadline}
              onChange={(e) => setDeadline(e.target.value)}
            />
          </div>
          <div>
            <label>Choices:</label>
            {choices.map((choice, index) => (
              <div className="choices" key={index}>
                <input
                  type="text"
                  value={choice}
                  onChange={(e) => handleChoiceChange(index, e.target.value)}
                />
                <button
                  type="button"
                  onClick={() => handleRemoveChoice(index)}
                >X
                </button>
              </div>
            ))}
            <button type="button" onClick={handleAddChoice}>
              Add Choice
            </button>
          </div>
          <div>
            <button type="submit">Create Poll</button>
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
