// src/pages/HomePage.tsx
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { fetchAllItems } from '../api';
import { useWishlist } from '../context/WishlistContext';
import Card from '../components/Card';
import type { MediaItem } from '../types';
import './HomePage.css';

export default function HomePage() {
  const [items, setItems] = useState<MediaItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [heroIndex, setHeroIndex] = useState(0);
  const navigate = useNavigate();
  const { toggleWishlist, isInWishlist } = useWishlist();

  useEffect(() => {
    fetchAllItems()
      .then(setItems)
      .finally(() => setLoading(false));
  }, []);

  // Rotate hero every 6 seconds
  useEffect(() => {
    if (items.length === 0) return;
    const interval = setInterval(() => {
      setHeroIndex((i) => (i + 1) % Math.min(5, items.length));
    }, 6000);
    return () => clearInterval(interval);
  }, [items.length]);

  const topRated = [...items].sort((a, b) => b.rating - a.rating).slice(0, 8);
  const hero = items[heroIndex];

  if (loading) {
    return (
      <div className="home-page">
        <div className="home-page__hero-skeleton" />
        <div className="home-page__section">
          <div className="home-page__skeleton-grid">
            {Array.from({ length: 8 }).map((_, i) => (
              <div key={i} className="home-page__skeleton-card" style={{ animationDelay: `${i * 60}ms` }} />
            ))}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="home-page">
      {/* Hero */}
      {hero && (
        <div className="home-page__hero">
          <div
            className="home-page__hero-bg"
            style={{ backgroundImage: `url(${hero.posterUrl})` }}
          />
          <div className="home-page__hero-overlay" />

          <div className="home-page__hero-content">
            <div className="home-page__hero-badges">
              <span className="home-page__badge home-page__badge--type">
                {hero.type === 'movie' ? '🎬 Movie' : '📺 Series'}
              </span>
              <span className="home-page__badge home-page__badge--rating">
                ★ {hero.rating.toFixed(1)}
              </span>
            </div>

            <h1 className="home-page__hero-title">{hero.title}</h1>

            <div className="home-page__hero-genres">
              {hero.genre.map((g) => (
                <span key={g} className="home-page__genre-pill">{g}</span>
              ))}
            </div>

            <p className="home-page__hero-overview">{hero.overview}</p>

            <div className="home-page__hero-actions">
              <button
                className="home-page__hero-btn home-page__hero-btn--primary"
                onClick={() => navigate(`/details/${hero.type}/${hero.id}`)}
              >
                ▶ View Details
              </button>
              <button
                className={`home-page__hero-btn home-page__hero-btn--secondary${isInWishlist(hero.id) ? ' active' : ''}`}
                onClick={() => toggleWishlist(hero)}
              >
                {isInWishlist(hero.id) ? '♥ In My List' : '♡ Add to My List'}
              </button>
            </div>
          </div>

          {/* Dots indicator */}
          <div className="home-page__hero-dots">
            {items.slice(0, Math.min(5, items.length)).map((_, i) => (
              <button
                key={i}
                className={`home-page__hero-dot${i === heroIndex ? ' active' : ''}`}
                onClick={() => setHeroIndex(i)}
                aria-label={`Slide ${i + 1}`}
              />
            ))}
          </div>
        </div>
      )}

      {/* Top rated section */}
      <section className="home-page__section">
        <div className="home-page__section-header">
          <h2 className="home-page__section-title">
            <span className="home-page__section-accent">◈</span> Top Rated
          </h2>
          <div className="home-page__section-links">
            <button className="home-page__see-all" onClick={() => navigate('/movies')}>
              Movies →
            </button>
            <button className="home-page__see-all" onClick={() => navigate('/tv-shows')}>
              TV Shows →
            </button>
          </div>
        </div>

        <div className="home-page__grid">
          {topRated.map((item) => (
            <Card key={item.id} item={item} />
          ))}
        </div>
      </section>
    </div>
  );
}
