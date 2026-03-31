import { useState } from "react";
import PersonForm from "./components/PersonForm.jsx";
import CardList from "./Components/CardList.jsx";
import "./styles/variables.css";
import "./styles/App.css";

/**
 * App — Root component
 *
 * State lives here (lifting state up pattern).
 * PersonForm  →  calls onSubmit(person)  →  App adds to `people` array
 * CardList    ←  receives `people` + onRemove  →  App filters out removed card
 */
export default function App() {
  const [people, setPeople] = useState([]);

  const handleAddPerson = (person) => {
    setPeople((prev) => [person, ...prev]);
  };

  const handleRemovePerson = (id) => {
    setPeople((prev) => prev.filter((p) => p.id !== id));
  };

  return (
    <div className="app-root">
      {/* Ambient background blobs */}
      <div className="app-blob app-blob--green" />
      <div className="app-blob app-blob--blue" />

      {/* Header */}
      <header className="app-header">
        <div className="app-header__logo">⬡</div>
        <div>
          <h1 className="app-header__title">People Board</h1>
          <p className="app-header__subtitle">
            Add people, see them appear as cards instantly.
          </p>
        </div>
      </header>

      {/* Main layout */}
      <main className="app-main">
        {/* Left panel — Form */}
        <aside className="app-sidebar">
          <PersonForm onSubmit={handleAddPerson} />
        </aside>

        {/* Right panel — Cards */}
        <section className="app-content">
          <CardList people={people} onRemove={handleRemovePerson} />
        </section>
      </main>
    </div>
  );
}
