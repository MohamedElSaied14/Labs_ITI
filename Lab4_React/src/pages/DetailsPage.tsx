// src/pages/DetailsPage.tsx
import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { fetchItemById } from '../api';
import { useWishlist } from '../context/WishlistContext';
import type { MediaItem } from '../types';
import './DetailsPage.css';

function RatingBar({ rating }: { rating: number }) {
  const pct = (rating / 10) * 100;
  return (
    <div className="details-page__rating-bar-wrap">
      <div className="details-page__rating-bar">
        <div
          className="details-page__rating-bar-fill"
          style={{ width: `${pct}%` }}
        />
      </div>
    </div>
  );
}

export default function DetailsPage() {
  const { type, id } = useParams<{ type: string; id: string }>();
  const navigate = useNavigate();
  const { isInWishlist, toggleWishlist } = useWishlist();
  const [item, setItem] = useState<MediaItem | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [imgLoaded, setImgLoaded] = useState(false);

  useEffect(() => {
    if (!type || !id) return;
    setLoading(true);
    setItem(null);
    setImgLoaded(false);
    const endpoint = type === 'movie' ? 'movies' : 'tvShows';
    fetchItemById(id, endpoint)
      .then(setItem)
      .catch(() => setError('Could not load this title.'))
      .finally(() => setLoading(false));
  }, [type, id]);

  if (loading) {
    return (
      <div className="details-page">
        <div className="details-page__loading">
          <div className="details-page__spinner" />
          <p>Loading…</p>
        </div>
      </div>
    );
  }

  if (error || !item) {
    return (
      <div className="details-page">
        <div className="details-page__error">
          <span>😕</span>
          <p>{error || 'Title not found.'}</p>
          <button onClick={() => navigate(-1)}>← Go back</button>
        </div>
      </div>
    );
  }

  const favored = isInWishlist(item.id);

  return (
    <div className="details-page">
      {/* Blurred backdrop */}
      <div
        className="details-page__backdrop"
        style={{ backgroundImage: `url(${item.posterUrl})` }}
      />
      <div className="details-page__backdrop-overlay" />

      {/* Content */}
      <div className="details-page__content">
        <button className="details-page__back" onClick={() => navigate(-1)}>
          ← Back
        </button>

        <div className="details-page__layout">
          {/* Poster */}
          <div className="details-page__poster-wrap">
            <img
              src={item.posterUrl}
              alt={item.title}
              className={`details-page__poster${imgLoaded ? ' loaded' : ''}`}
              onLoad={() => setImgLoaded(true)}
              onError={(e) => {
                (e.target as HTMLImageElement).src =
                  `https://placehold.co/300x450/0f0f1a/6b697f?text=${encodeURIComponent(item.title)}`;
                setImgLoaded(true);
              }}
            />
          </div>

          {/* Info */}
          <div className="details-page__info">
            <div className="details-page__top-meta">
              <span className="details-page__type-badge">
                {item.type === 'movie' ? '🎬 Movie' : '📺 Series'}
              </span>
              <span className="details-page__year">{item.year}</span>
            </div>

            <h1 className="details-page__title">{item.title}</h1>

            {/* Rating */}
            <div className="details-page__rating-section">
              <div className="details-page__rating-row">
                <span className="details-page__rating-star">★</span>
                <span className="details-page__rating-val">{item.rating.toFixed(1)}</span>
                <span className="details-page__rating-max">/ 10</span>
              </div>
              <RatingBar rating={item.rating} />
            </div>

            {/* Genres */}
            <div className="details-page__genres">
              {item.genre.map((g) => (
                <span key={g} className="details-page__genre-tag">{g}</span>
              ))}
            </div>

            {/* Overview */}
            <div className="details-page__overview-wrap">
              <h2 className="details-page__overview-label">Overview</h2>
              <p className="details-page__overview">{item.overview}</p>
            </div>

            {/* Action */}
            <button
              className={`details-page__fav-btn${favored ? ' active' : ''}`}
              onClick={() => toggleWishlist(item)}
            >
              {favored ? '♥ In My List' : '♡ Add to My List'}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
