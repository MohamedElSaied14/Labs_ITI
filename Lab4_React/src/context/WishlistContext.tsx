// src/context/WishlistContext.tsx
import { createContext, useContext, useReducer, useEffect, type ReactNode } from 'react';
import type { MediaItem, WishlistAction, WishlistState } from '../types';

const LS_KEY = 'cinescope_wishlist';

function reducer(state: WishlistState, action: WishlistAction): WishlistState {
  switch (action.type) {
    case 'ADD':
      if (state.items.some((i) => i.id === action.payload.id)) return state;
      return { items: [...state.items, action.payload] };
    case 'REMOVE':
      return { items: state.items.filter((i) => i.id !== action.payload) };
    case 'TOGGLE': {
      const exists = state.items.some((i) => i.id === action.payload.id);
      return exists
        ? { items: state.items.filter((i) => i.id !== action.payload.id) }
        : { items: [...state.items, action.payload] };
    }
    case 'HYDRATE':
      return { items: action.payload };
    default:
      return state;
  }
}

interface WishlistContextValue {
  wishlist: MediaItem[];
  isInWishlist: (id: string) => boolean;
  toggleWishlist: (item: MediaItem) => void;
  removeFromWishlist: (id: string) => void;
}

const WishlistContext = createContext<WishlistContextValue | null>(null);

export function WishlistProvider({ children }: { children: ReactNode }) {
  const [state, dispatch] = useReducer(reducer, { items: [] });

  // Hydrate from localStorage on mount
  useEffect(() => {
    try {
      const stored = localStorage.getItem(LS_KEY);
      if (stored) {
        const parsed: MediaItem[] = JSON.parse(stored);
        dispatch({ type: 'HYDRATE', payload: parsed });
      }
    } catch {
      // ignore corrupt storage
    }
  }, []);

  // Persist to localStorage on every change
  useEffect(() => {
    localStorage.setItem(LS_KEY, JSON.stringify(state.items));
  }, [state.items]);

  const isInWishlist = (id: string) => state.items.some((i) => i.id === id);

  const toggleWishlist = (item: MediaItem) => {
    dispatch({ type: 'TOGGLE', payload: item });
    // Optimistic server sync (fire-and-forget)
    const endpoint = item.type === 'movie' ? 'movies' : 'tvShows';
    const nowFavored = !isInWishlist(item.id);
    fetch(`/api/${endpoint}/${item.id}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ favored: nowFavored }),
    }).catch(() => {/* server might be off, localStorage is source of truth */});
  };

  const removeFromWishlist = (id: string) => {
    dispatch({ type: 'REMOVE', payload: id });
  };

  return (
    <WishlistContext.Provider value={{ wishlist: state.items, isInWishlist, toggleWishlist, removeFromWishlist }}>
      {children}
    </WishlistContext.Provider>
  );
}

export function useWishlist() {
  const ctx = useContext(WishlistContext);
  if (!ctx) throw new Error('useWishlist must be used inside WishlistProvider');
  return ctx;
}
