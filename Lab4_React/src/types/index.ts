// src/types/index.ts

export type ItemType = 'movie' | 'tvShow';

export interface MediaItem {
  id: string;
  title: string;
  type: ItemType;
  genre: string[];
  rating: number;
  overview: string;
  posterUrl: string;
  year: number;
  favored?: boolean;
}

export interface WishlistState {
  items: MediaItem[];
}

export type WishlistAction =
  | { type: 'ADD'; payload: MediaItem }
  | { type: 'REMOVE'; payload: string }          // id
  | { type: 'TOGGLE'; payload: MediaItem }
  | { type: 'HYDRATE'; payload: MediaItem[] };

export interface FiltersState {
  search: string;
  genres: string[];
  minRating: number;
}
