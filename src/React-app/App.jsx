import React, { useEffect, useState } from "react";
import "./App.css";

const apiUrl = "http://localhost:3000/api";

function App() {
  const [bookmarks, setBookmarks] = useState([]);
  const [title, setTitle] = useState("");
  const [url, setUrl] = useState("");
  const [searchTerm, setSearchTerm] = useState("");
  const [error, setError] = useState("");
  const [isEdit, setIsEdit] = useState(false);
  const [editId, setEditId] = useState(null);

  useEffect(() => {
    fetchAllBookmarks();
  }, []);

  const fetchAllBookmarks = async () => {
    try {
      const response = await fetch(`${apiUrl}/readAll.php`);
      const data = await response.json();
      setBookmarks(data);
    } catch (err) {
      console.error(err);
    }
  };

  const addNewBookmark = async () => {
    if (title.trim() && url.trim()) {
      const options = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title, link: url })
      };

      await fetch(`${apiUrl}/create.php`, options);
      resetForm();
      fetchAllBookmarks();
    } else {
      setError("Both URL and Title are required.");
    }
  };

  const updateBookmark = async () => {
    if (editId && title.trim() && url.trim()) {
      const options = {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: editId, title, link: url })
      };

      await fetch(`${apiUrl}/update.php`, options);
      resetForm();
      fetchAllBookmarks();
    }
  };

  const deleteBookmark = async (id) => {
    const options = {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    };

    await fetch(`${apiUrl}/delete.php`, options);
    fetchAllBookmarks();
  };

  const handleEdit = (item) => {
    setTitle(item.title);
    setUrl(item.link);
    setEditId(item.id);
    setIsEdit(true);
  };

  const resetForm = () => {
    setTitle("");
    setUrl("");
    setIsEdit(false);
    setEditId(null);
    setError("");
  };

  return (
    <div className="container">
      <h2>Your Bookmarks</h2>

      <input
        type="text"
        className="search-box"
        placeholder="Search bookmarks..."
        value={searchTerm}
        onChange={(e) => setSearchTerm(e.target.value)}
      />

      <div className="add-form">
        <input
          type="text"
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          placeholder="Enter website name"
        />
        <input
          type="text"
          value={url}
          onChange={(e) => setUrl(e.target.value)}
          placeholder="Enter URL"
        />
        <button onClick={isEdit ? updateBookmark : addNewBookmark}>
          {isEdit ? "Update Bookmark" : "Add Bookmark"}
        </button>
        {isEdit && (
          <button className="cancel-btn" onClick={resetForm}>
            Cancel
          </button>
        )}
      </div>

      {error && <div style={{ color: "red", marginTop: "10px" }}>{error}</div>}

      <ul className="bookmark-list">
        {bookmarks
          .filter((item) =>
            item.title.toLowerCase().includes(searchTerm.toLowerCase())
          )
          .map((item) => (
            <li key={item.id} className="bookmark">
              <a href={item.link} target="_blank" rel="noopener noreferrer">
                <div className="bookmark-info">
                  <strong>{item.title}</strong>
                  <small>{item.date_added}</small>
                </div>
              </a>
              <div style={{ display: "flex", gap: "8px" }}>
                <button className="delete-btn" onClick={() => deleteBookmark(item.id)}>
                  Delete
                </button>
                <button
                  className="delete-btn"
                  style={{ backgroundColor: "#007bff" }}
                  onClick={() => handleEdit(item)}
                >
                  Update a bookmark
                </button>
              </div>
            </li>
          ))}
      </ul>
    </div>
  );
}

export default App;
