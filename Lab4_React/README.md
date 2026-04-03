# CineScope 🎬

A movie & TV show catalog built with React 18 + TypeScript, Context API, React Router v6, and JSON Server.

---

## Quick Start

```bash
# 1. Install dependencies
npm install

# 2. Run JSON Server (mock API) + Vite dev server together
npm run dev:all
```

Then open **http://localhost:5173**

> Vite proxies `/api/*` → `http://localhost:3001` so CORS is not an issue.

---

## Run separately (two terminals)

```bash
# Terminal 1 — JSON Server on :3001
npm run server

# Terminal 2 — Vite on :5173
npm run dev
```

---

## Project Structure

```
cinescope/
├── server/
│   └── db.json                     ← Mock data: 8 movies + 8 TV shows
├── src/
│   ├── types/index.ts              ← MediaItem, WishlistAction, FiltersState
│   ├── api/index.ts                ← fetch / patch / delete / create / update
│   ├── context/
│   │   └── WishlistContext.tsx     ← useReducer + localStorage persistence
│   ├── components/
│   │   ├── NavBar.tsx / .css       ← Sticky nav, search bar, wishlist badge
│   │   ├── FiltersBar.tsx / .css   ← Rating slider + genre multi-select chips
│   │   └── Card.tsx / .css         ← Poster card with ♥ toggle and ✕ remove
│   ├── pages/
│   │   ├── HomePage.tsx / .css     ← Auto-rotating hero + top-rated grid
│   │   ├── ListPage.tsx / .css     ← Movies / TV Shows with sort + filters
│   │   ├── DetailsPage.tsx / .css  ← Full detail view at /details/:type/:id
│   │   └── MyListPage.tsx / .css   ← Saved wishlist with tab filters
│   ├── styles/
│   │   └── variables.css           ← Cinematic dark design tokens
│   ├── App.tsx                     ← createBrowserRouter + WishlistProvider
│   └── main.tsx                    ← ReactDOM.createRoot entry
├── package.json
├── tsconfig.json
└── vite.config.ts
```

---

## Features (MVP)

| Feature | Details |
|---|---|
| **NavBar** | Sticky, glassmorphic, links, animated search bar, wishlist count badge |
| **Card grid** | Poster, title, genres, rating, ♥ toggle, ✕ remove (My List) |
| **Details page** | `/details/:type/:id` — backdrop blur, rating bar, full overview, back button |
| **Rating filter** | Range slider (0–10, step 0.5) + clickable star row |
| **Genre filter** | Multi-select chips for 11 genres |
| **Sort** | By Rating / Year / Title on list pages |
| **Search** | Client-side title filter wired through App-level state |
| **My List** | Wishlist tab-filtered by All / Movies / Shows, empty-state CTA |
| **Context + useReducer** | `WishlistContext` — ADD / REMOVE / TOGGLE / HYDRATE actions |
| **localStorage** | Wishlist persists across page reloads |
| **Optimistic UI** | Wishlist updates instantly; `PATCH` fires async to JSON Server |
| **TypeScript** | Strict types throughout — `MediaItem`, `WishlistAction`, `FiltersState` |

---

## API Endpoints (JSON Server)

| Method | URL | Action |
|---|---|---|
| GET | `/api/movies` | All movies |
| GET | `/api/tvShows` | All TV shows |
| GET | `/api/movies/:id` | Single movie |
| PATCH | `/api/movies/:id` | Update `favored` flag |
| POST | `/api/movies` | Create new movie |
| PUT | `/api/movies/:id` | Replace movie |
| DELETE | `/api/movies/:id` | Delete movie |

Same endpoints exist for `/api/tvShows`.

---

## Routes

| Path | Page |
|---|---|
| `/` | HomePage — hero carousel + top-rated grid |
| `/movies` | ListPage — movies with search/filter/sort |
| `/tv-shows` | ListPage — TV shows with search/filter/sort |
| `/my-list` | MyListPage — saved wishlist |
| `/details/:type/:id` | DetailsPage — full item view |

---

## Phase 2 Roadmap

- [ ] Add/Edit item modal form (POST / PUT to JSON Server)
- [ ] Auth simulation (mock login → Context, protected routes)
- [ ] Infinite scroll / pagination
- [ ] More CRUD: inline edit on MyListPage
- [ ] Accessibility audit (ARIA, keyboard navigation, focus management)
- [ ] `React.memo` + `useCallback` performance pass
- [ ] Dark/light theme toggle
- [ ] Mobile bottom tab bar
