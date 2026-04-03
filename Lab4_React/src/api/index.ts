// src/api/index.ts
import type { MediaItem } from '../types';

const BASE = '/api';

export async function fetchMovies(): Promise<MediaItem[]> {
  const res = await fetch(`${BASE}/movies`);
  if (!res.ok) throw new Error('Failed to fetch movies');
  return res.json();
}

export async function fetchTvShows(): Promise<MediaItem[]> {
  const res = await fetch(`${BASE}/tvShows`);
  if (!res.ok) throw new Error('Failed to fetch TV shows');
  return res.json();
}

export async function fetchAllItems(): Promise<MediaItem[]> {
  const [movies, shows] = await Promise.all([fetchMovies(), fetchTvShows()]);
  return [...movies, ...shows];
}

export async function fetchItemById(id: string, type: 'movies' | 'tvShows'): Promise<MediaItem> {
  const res = await fetch(`${BASE}/${type}/${id}`);
  if (!res.ok) throw new Error('Item not found');
  return res.json();
}

// Optimistic: caller updates UI immediately, this syncs to server
export async function patchFavored(id: string, type: 'movies' | 'tvShows', favored: boolean): Promise<void> {
  await fetch(`${BASE}/${type}/${id}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ favored }),
  });
}

export async function deleteItem(id: string, type: 'movies' | 'tvShows'): Promise<void> {
  await fetch(`${BASE}/${type}/${id}`, { method: 'DELETE' });
}

export async function createItem(item: Omit<MediaItem, 'id'>): Promise<MediaItem> {
  const endpoint = item.type === 'movie' ? 'movies' : 'tvShows';
  const res = await fetch(`${BASE}/${endpoint}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(item),
  });
  if (!res.ok) throw new Error('Failed to create item');
  return res.json();
}

export async function updateItem(item: MediaItem): Promise<MediaItem> {
  const endpoint = item.type === 'movie' ? 'movies' : 'tvShows';
  const res = await fetch(`${BASE}/${endpoint}/${item.id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(item),
  });
  if (!res.ok) throw new Error('Failed to update item');
  return res.json();
}
