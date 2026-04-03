// src/pages/ListPage.tsx
import { useState, useEffect, useMemo } from 'react';
import { fetchMovies, fetchTvShows } from '../api';
import type { MediaItem, FiltersState } from '../types';
import Card from '../components/Card';
import FiltersBar from '../components/FiltersBar';
import './ListPage.css';

interface ListPageProps {
  type: 'movie' | 'tvShow';
  search: string;
}

export default function ListPage({ type, search }: ListPageProps) {
  const [items, setItems] = useState<MediaItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState<FiltersState>({ search: '', genres: [], minRating: 0 });
  const [showFilters, setShowFilters] = useState(false);
  const [sort, setSort] = useState<'rating' | 'year' | 'title'>('rating');

  useEffect(() => {
    setLoading(true);
    setItems([]);
    const fetcher = type === 'movie' ? fetchMovies : fetchTvShows;
    fetcher()
      .then(setItems)
      .finally(() => setLoading(false));
  }, [type]);

  const filtered = useMemo(() => {
    let result = items.filter((item) => {
      const q = (search || '').toLowerCase().trim();
      if (q && !item.title.toLowerCase().includes(q)) return false;
      if (filters.minRating > 0 && item.rating < filters.minRating) return false;
      if (filters.genres.length > 0 && !filters.genres.some((g) => item.genre.includes(g))) return false;
      return true;
    });

    result = [...result].sort((a, b) => {
      if (sort === 'rating') return b.rating - a.rating;
      if (sort === 'year')   return b.year - a.year;
      if (sort === 'title')  return a.title.localeCompare(b.title);
      return 0;
    });

    return result;
  }, [items, search, filters, sort]);

  const activeFilterCount =
    filters.genres.length + (filters.minRating > 0 ? 1 : 0);

  return (
    <div className="list-page">
      <div className="list-page__inner">
        {/* Header */}
        <div className="list-page__header">
          <div>
            <h1 className="list-page__title">
              {type === 'movie' ? 'Movies' : 'TV Shows'}
            </h1>
            <p className="list-page__count">
              {loading
                ? 'Loading…'
                : `${filtered.length} title${filtered.length !== 1 ? 's' : ''}`}
              {search && (
                <span className="list-page__search-badge">for "{search}"</span>
              )}
            </p>
          </div>

          <div className="list-page__controls">
            {/* Sort */}
            <div className="list-page__sort">
              <span className="list-page__sort-label">Sort</span>
              {(['rating', 'year', 'title'] as const).map((s) => (
                <button
                  key={s}
                  className={`list-page__sort-btn${sort === s ? ' active' : ''}`}
                  onClick={() => setSort(s)}
                >
                  {s === 'rating' ? '★ Rating' : s === 'year' ? '📅 Year' : '🔤 Title'}
                </button>
              ))}
            </div>

            {/* Filters toggle */}
            <button
              className={`list-page__filter-toggle${showFilters ? ' active' : ''}`}
              onClick={() => setShowFilters((v) => !v)}
            >
              ⚙ Filters
              {activeFilterCount > 0 && (
                <span className="list-page__filter-badge">{activeFilterCount}</span>
              )}
            </button>
          </div>
        </div>

        {/* FiltersBar */}
        {showFilters && (
          <FiltersBar filters={filters} onChange={setFilters} />
        )}

        {/* Content */}
        {loading ? (
          <div className="list-page__grid">
            {Array.from({ length: 8 }).map((_, i) => (
              <div
                key={i}
                className="list-page__skeleton"
                style={{ animationDelay: `${i * 50}ms` }}
              />
            ))}
          </div>
        ) : filtered.length === 0 ? (
          <div className="list-page__empty">
            <div className="list-page__empty-icon">🎬</div>
            <p className="list-page__empty-title">No titles found</p>
            <p className="list-page__empty-sub">Try adjusting your filters or search query.</p>
            <button
              className="list-page__empty-reset"
              onClick={() => setFilters({ search: '', genres: [], minRating: 0 })}
            >
              Reset filters
            </button>
          </div>
        ) : (
          <div className="list-page__grid">
            {filtered.map((item) => (
              <Card key={item.id} item={item} />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
