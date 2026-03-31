import { useState } from "react";
import "../styles/CardList.css";

/* ── Helpers ──────────────────────────────────────────────── */

function getInitials(name) {
  return name
    .split(" ")
    .map((w) => w[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
}

function getAgeGroup(age) {
  if (age < 13) return { label: "Child",       color: "#45e0ff" };
  if (age < 18) return { label: "Teen",        color: "#b845ff" };
  if (age < 30) return { label: "Young Adult", color: "#b8ff45" };
  if (age < 60) return { label: "Adult",       color: "#ffaa45" };
  return           { label: "Senior",      color: "#ff6b6b" };
}

// Each index maps to [accent, avatarTextColor]
const PALETTE = [
  ["#b8ff45", "#0a1f00"],
  ["#45e0ff", "#001a20"],
  ["#b845ff", "#1a0020"],
  ["#ffaa45", "#201000"],
  ["#ff6b6b", "#200000"],
];

/* ── PersonCard ───────────────────────────────────────────── */

function PersonCard({ person, index, onRemove }) {
  const [hovered, setHovered] = useState(false);
  const ageGroup = getAgeGroup(person.age);
  const [accent, avatarTextColor] = PALETTE[index % PALETTE.length];

  return (
    <div
      className="person-card"
      style={{
        borderColor: hovered ? accent : "var(--border-default)",
        boxShadow: hovered
          ? `0 20px 50px rgba(0,0,0,0.6), 0 0 0 1px ${accent}40`
          : "var(--shadow-card)",
      }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
    >
      {/* Coloured top bar */}
      <div
        className="person-card__accent-bar"
        style={{ background: accent }}
      />

      {/* Initials avatar */}
      <div
        className="person-card__avatar"
        style={{ background: accent, color: avatarTextColor }}
      >
        {getInitials(person.name)}
      </div>

      {/* Info */}
      <h3 className="person-card__name">{person.name}</h3>

      <p className="person-card__age-row">
        <span className="person-card__age-num">{person.age}</span>
        <span className="person-card__age-label">yrs</span>
      </p>

      <span
        className="person-card__badge"
        style={{
          background:   `${accent}22`,
          color:        accent,
          borderColor:  `${accent}55`,
        }}
      >
        {ageGroup.label}
      </span>

      {/* Remove button */}
      <button
        className="person-card__remove"
        onClick={() => onRemove(person.id)}
        title="Remove"
      >
        ✕
      </button>
    </div>
  );
}

/* ── CardList ─────────────────────────────────────────────── */

export default function CardList({ people, onRemove }) {
  if (people.length === 0) {
    return (
      <div className="cardlist-empty">
        <div className="cardlist-empty__icon">◎</div>
        <p className="cardlist-empty__text">No cards yet.</p>
        <p className="cardlist-empty__subtext">Submit the form to add people.</p>
      </div>
    );
  }

  return (
    <div className="cardlist-wrapper">
      <div className="cardlist-header">
        <span className="cardlist-header__count">{people.length}</span>
        <span className="cardlist-header__label">
          {people.length === 1 ? "Person" : "People"} Added
        </span>
      </div>

      <div className="cardlist-grid">
        {people.map((person, i) => (
          <PersonCard
            key={person.id}
            person={person}
            index={i}
            onRemove={onRemove}
          />
        ))}
      </div>
    </div>
  );
}
