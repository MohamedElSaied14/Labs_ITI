// src/pages/MyListPage.tsx
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useWishlist } from '../context/WishlistContext';
import Card from '../components/Card';
import './MyListPage.css';

export default function MyListPage() {
  const { wishlist } = useWishlist();
  const navigate = useNavigate();
  const [filter, setFilter] = useState<'all' | 'movie' | 'tvShow'>('all');

  const filtered = wishlist.filter((item) =>
    filter === 'all' ? true : item.type === filter
  );

  const movieCount = wishlist.filter((i) => i.type === 'movie').length;
  const tvCount    = wishlist.filter((i) => i.type === 'tvShow').length;

  return (
    <div className="mylist-page">
      <div className="mylist-page__inner">
        {/* Header */}
        <div className="mylist-page__header">
          <div>
            <h1 className="mylist-page__title">My List</h1>
            <p className="mylist-page__subtitle">
              {wishlist.length} saved title{wishlist.length !== 1 ? 's' : ''}
            </p>
          </div>

          {wishlist.length > 0 && (
            <div className="mylist-page__tabs">
              {([
                { key: 'all',    label: `All (${wishlist.length})` },
                { key: 'movie',  label: `🎬 Movies (${movieCount})` },
                { key: 'tvShow', label: `📺 Shows (${tvCount})` },
              ] as const).map(({ key, label }) => (
                <button
                  key={key}
                  className={`mylist-page__tab${filter === key ? ' active' : ''}`}
                  onClick={() => setFilter(key)}
                >
                  {label}
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Empty state */}
        {wishlist.length === 0 ? (
          <div className="mylist-page__empty">
            <div className="mylist-page__empty-icon">♡</div>
            <h2 className="mylist-page__empty-title">Your list is empty</h2>
            <p className="mylist-page__empty-sub">
              Browse movies and TV shows and add your favorites here.
            </p>
            <div className="mylist-page__empty-actions">
              <button className="mylist-page__cta" onClick={() => navigate('/movies')}>
                Browse Movies
              </button>
              <button className="mylist-page__cta mylist-page__cta--outline" onClick={() => navigate('/tv-shows')}>
                Browse TV Shows
              </button>
            </div>
          </div>
        ) : filtered.length === 0 ? (
          <div className="mylist-page__empty">
            <p className="mylist-page__empty-sub">No {filter === 'movie' ? 'movies' : 'TV shows'} in your list yet.</p>
          </div>
        ) : (
          <div className="mylist-page__grid">
            {filtered.map((item) => (
              <Card key={item.id} item={item} showRemove />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
