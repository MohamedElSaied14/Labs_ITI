// src/components/Card.tsx
import { useNavigate } from 'react-router-dom';
import { useWishlist } from '../context/WishlistContext';
import type { MediaItem } from '../types';
import './Card.css';

interface CardProps {
  item: MediaItem;
  showRemove?: boolean;
}

export default function Card({ item, showRemove = false }: CardProps) {
  const navigate = useNavigate();
  const { isInWishlist, toggleWishlist, removeFromWishlist } = useWishlist();
  const favored = isInWishlist(item.id);

  return (
    <article
      className="card"
      onClick={() => navigate(`/details/${item.type}/${item.id}`)}
      role="button"
      tabIndex={0}
      onKeyDown={(e) => e.key === 'Enter' && navigate(`/details/${item.type}/${item.id}`)}
    >
      {/* Poster */}
      <div className="card__poster-wrap">
        <img
          className="card__poster"
          src={item.posterUrl}
          alt={item.title}
          loading="lazy"
          onError={(e) => {
            (e.target as HTMLImageElement).src =
              `https://placehold.co/200x300/0f0f1a/6b697f?text=${encodeURIComponent(item.title)}`;
          }}
        />
        <div className="card__overlay">
          <span className="card__type-badge">
            {item.type === 'movie' ? '🎬 Movie' : '📺 Series'}
          </span>
        </div>
      </div>

      {/* Body */}
      <div className="card__body">
        <div className="card__meta">
          <span className="card__year">{item.year}</span>
          <span className="card__rating">
            <span className="card__rating-star">★</span>
            {item.rating.toFixed(1)}
          </span>
        </div>

        <h3 className="card__title">{item.title}</h3>

        <div className="card__genres">
          {item.genre.slice(0, 3).map((g) => (
            <span key={g} className="card__genre-tag">{g}</span>
          ))}
        </div>
      </div>

      {/* Actions — stop propagation so clicks don't navigate */}
      <div className="card__actions" onClick={(e) => e.stopPropagation()}>
        <button
          className={`card__fav-btn${favored ? ' card__fav-btn--active' : ''}`}
          onClick={() => toggleWishlist(item)}
          aria-label={favored ? 'Remove from My List' : 'Add to My List'}
          title={favored ? 'Remove from My List' : 'Add to My List'}
        >
          {favored ? '♥' : '♡'}
        </button>

        {showRemove && (
          <button
            className="card__remove-btn"
            onClick={() => removeFromWishlist(item.id)}
            aria-label="Remove from list"
            title="Remove from list"
          >
            ✕
          </button>
        )}
      </div>
    </article>
  );
}
