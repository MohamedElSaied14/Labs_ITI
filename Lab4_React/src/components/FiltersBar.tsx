// src/components/FiltersBar.tsx
import type { FiltersState } from '../types';
import './FiltersBar.css';

const ALL_GENRES = [
  'Action', 'Adventure', 'Comedy', 'Crime', 'Drama',
  'Fantasy', 'History', 'Horror', 'Mystery', 'Sci-Fi', 'Thriller',
];

interface FiltersBarProps {
  filters: FiltersState;
  onChange: (f: FiltersState) => void;
}

export default function FiltersBar({ filters, onChange }: FiltersBarProps) {
  const toggleGenre = (g: string) => {
    const has = filters.genres.includes(g);
    onChange({
      ...filters,
      genres: has ? filters.genres.filter((x) => x !== g) : [...filters.genres, g],
    });
  };

  const clearAll = () => onChange({ search: filters.search, genres: [], minRating: 0 });

  const hasFilters = filters.genres.length > 0 || filters.minRating > 0;

  return (
    <div className="filters">
      <div className="filters__section">
        <div className="filters__label">
          <span>Rating</span>
          <span className="filters__value">{filters.minRating > 0 ? `≥ ${filters.minRating.toFixed(1)}` : 'All'}</span>
        </div>
        <div className="filters__slider-wrap">
          <span className="filters__slider-min">0</span>
          <input
            type="range"
            className="filters__slider"
            min={0}
            max={10}
            step={0.5}
            value={filters.minRating}
            onChange={(e) => onChange({ ...filters, minRating: parseFloat(e.target.value) })}
          />
          <span className="filters__slider-max">10</span>
        </div>
        {/* Stars visual */}
        <div className="filters__stars">
          {Array.from({ length: 10 }).map((_, i) => (
            <span
              key={i}
              className={`filters__star${i < Math.round(filters.minRating) ? ' filters__star--on' : ''}`}
              onClick={() => onChange({ ...filters, minRating: i + 1 === filters.minRating ? 0 : i + 1 })}
            >★</span>
          ))}
        </div>
      </div>

      <div className="filters__divider" />

      <div className="filters__section">
        <div className="filters__label">
          <span>Genres</span>
          {filters.genres.length > 0 && (
            <span className="filters__count">{filters.genres.length} selected</span>
          )}
        </div>
        <div className="filters__genres">
          {ALL_GENRES.map((g) => (
            <button
              key={g}
              className={`filters__genre-chip${filters.genres.includes(g) ? ' filters__genre-chip--active' : ''}`}
              onClick={() => toggleGenre(g)}
            >
              {g}
            </button>
          ))}
        </div>
      </div>

      {hasFilters && (
        <button className="filters__clear" onClick={clearAll}>
          Clear filters ✕
        </button>
      )}
    </div>
  );
}
