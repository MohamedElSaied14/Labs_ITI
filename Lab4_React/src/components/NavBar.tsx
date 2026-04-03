// src/components/NavBar.tsx
import { useState, useRef, useEffect } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { useWishlist } from '../context/WishlistContext';
import './NavBar.css';

interface NavBarProps {
  onSearch: (q: string) => void;
  searchValue: string;
}

export default function NavBar({ onSearch, searchValue }: NavBarProps) {
  const { wishlist } = useWishlist();
  const navigate = useNavigate();
  const [searchOpen, setSearchOpen] = useState(false);
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (searchOpen) inputRef.current?.focus();
  }, [searchOpen]);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchValue.trim()) navigate('/movies');
  };

  return (
    <nav className="navbar">
      <div className="navbar__inner">
        {/* Logo */}
        <NavLink to="/" className="navbar__logo">
          <span className="navbar__logo-icon">◈</span>
          <span className="navbar__logo-text">CineScope</span>
        </NavLink>

        {/* Nav links */}
        <ul className="navbar__links">
          {[
            { to: '/', label: 'Home', end: true },
            { to: '/movies', label: 'Movies', end: false },
            { to: '/tv-shows', label: 'TV Shows', end: false },
            { to: '/my-list', label: 'My List', end: false },
          ].map(({ to, label, end }) => (
            <li key={to}>
              <NavLink
                to={to}
                end={end}
                className={({ isActive }) =>
                  `navbar__link${isActive ? ' navbar__link--active' : ''}`
                }
              >
                {label}
              </NavLink>
            </li>
          ))}
        </ul>

        {/* Right actions */}
        <div className="navbar__actions">
          {/* Search */}
          <form
            className={`navbar__search-form${searchOpen ? ' navbar__search-form--open' : ''}`}
            onSubmit={handleSearchSubmit}
          >
            {searchOpen && (
              <input
                ref={inputRef}
                className="navbar__search-input"
                placeholder="Search titles…"
                value={searchValue}
                onChange={(e) => onSearch(e.target.value)}
                onBlur={() => { if (!searchValue) setSearchOpen(false); }}
              />
            )}
            <button
              type="button"
              className="navbar__icon-btn"
              aria-label="Search"
              onClick={() => setSearchOpen((v) => !v)}
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </button>
          </form>

          {/* Wishlist badge */}
          <NavLink to="/my-list" className="navbar__wishlist-btn" aria-label="My List">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
            {wishlist.length > 0 && (
              <span className="navbar__badge">{wishlist.length}</span>
            )}
          </NavLink>
        </div>
      </div>
    </nav>
  );
}
