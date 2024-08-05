import React, { useState } from 'react';

function Home() {
  const [response, setResponse] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleButtonClick = async () => {
    setLoading(true);
    setError('');
    try {
      const res = await fetch('http://localhost:3035'); // Replace with your server endpoint
      if (!res.ok) {
        throw new Error('Network response was not ok');
      }
      const data = await res.json();
      setResponse(JSON.stringify(data, null, 2));
    } catch (err) {
      console.log(err);
      setError('Failed to fetch data');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ textAlign: 'center', marginTop: '50px' }}>
      <h1>Simple Home Screen</h1>
      <button onClick={handleButtonClick} disabled={loading}>
        {loading ? 'Loading...' : 'Fetch Data'}
      </button>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      {response && (
        <pre
          style={{
            textAlign: 'left',
            display: 'inline-block',
            padding: '10px',
            border: '1px solid #ddd',
            backgroundColor: '#f9f9f9',
            marginTop: '20px',
          }}
        >
          {response}
        </pre>
      )}
    </div>
  );
}

export default Home;
