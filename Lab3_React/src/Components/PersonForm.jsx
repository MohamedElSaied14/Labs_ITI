import { useState } from "react";
import "../styles/PersonForm.css";

export default function PersonForm({ onSubmit }) {
  const [name, setName] = useState("");
  const [age, setAge] = useState("");
  const [errors, setErrors] = useState({});
  const [shake, setShake] = useState(false);

  const validate = () => {
    const newErrors = {};
    if (!name.trim()) newErrors.name = "Name is required";
    if (!age || isNaN(age) || age < 1 || age > 120)
      newErrors.age = "Enter a valid age (1–120)";
    return newErrors;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const validationErrors = validate();
    if (Object.keys(validationErrors).length > 0) {
      setErrors(validationErrors);
      setShake(true);
      setTimeout(() => setShake(false), 500);
      return;
    }
    onSubmit({ name: name.trim(), age: Number(age), id: Date.now() });
    setName("");
    setAge("");
    setErrors({});
  };

  return (
    <div className="form-wrapper">
      <div className={`form-card ${shake ? "form-card--shake" : ""}`}>

        {/* Rainbow top bar */}
        <div className="form-card__top-bar" />

        {/* Header */}
        <div className="form-card__header">
          <span className="form-card__icon">✦</span>
          <h2 className="form-card__title">Add a Person</h2>
        </div>

        <form onSubmit={handleSubmit} noValidate>

          {/* Name field */}
          <div className="form-field">
            <label className="form-field__label" htmlFor="name">
              Full Name
            </label>
            <input
              id="name"
              type="text"
              placeholder="e.g. Layla Hassan"
              value={name}
              className={`form-field__input ${errors.name ? "form-field__input--error" : ""}`}
              onChange={(e) => {
                setName(e.target.value);
                if (errors.name) setErrors((p) => ({ ...p, name: "" }));
              }}
            />
            {errors.name && (
              <p className="form-field__error">{errors.name}</p>
            )}
          </div>

          {/* Age field */}
          <div className="form-field">
            <label className="form-field__label" htmlFor="age">
              Age
            </label>
            <input
              id="age"
              type="number"
              placeholder="e.g. 24"
              value={age}
              min={1}
              max={120}
              className={`form-field__input ${errors.age ? "form-field__input--error" : ""}`}
              onChange={(e) => {
                setAge(e.target.value);
                if (errors.age) setErrors((p) => ({ ...p, age: "" }));
              }}
            />
            {errors.age && (
              <p className="form-field__error">{errors.age}</p>
            )}
          </div>

          <button type="submit" className="form-btn">
            + Add Card
          </button>

        </form>
      </div>
    </div>
  );
}
