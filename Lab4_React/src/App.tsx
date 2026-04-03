// src/App.tsx
import { useState, useCallback } from 'react';
import { createBrowserRouter, RouterProvider, Outlet } from 'react-router-dom';
import { WishlistProvider } from './context/WishlistContext';
import NavBar from './components/NavBar';
import HomePage from './pages/HomePage';
import ListPage from './pages/ListPage';
import DetailsPage from './pages/DetailsPage';
import MyListPage from './pages/MyListPage';

// Layout wrapper gives all child routes access to NavBar + search state
function Layout({
  search,
  onSearch,
}: {
  search: string;
  onSearch: (q: string) => void;
}) {
  return (
    <>
      <NavBar onSearch={onSearch} searchValue={search} />
      <Outlet />
    </>
  );
}

export default function App() {
  const [search, setSearch] = useState('');
  const handleSearch = useCallback((q: string) => setSearch(q), []);

  // Router is stable — defined outside render via useMemo approach embedded here
  const router = createBrowserRouter([
    {
      path: '/',
      element: <Layout search={search} onSearch={handleSearch} />,
      children: [
        { index: true,                 element: <HomePage /> },
        { path: 'movies',              element: <ListPage type="movie"  search={search} /> },
        { path: 'tv-shows',            element: <ListPage type="tvShow" search={search} /> },
        { path: 'my-list',             element: <MyListPage /> },
        { path: 'details/:type/:id',   element: <DetailsPage /> },
      ],
    },
  ]);

  return (
    <WishlistProvider>
      <RouterProvider router={router} />
    </WishlistProvider>
  );
}
