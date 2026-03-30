import { Component } from "react";
import Movie from "./movie";

const API_URL = `https://jsonfakery.com/movies/paginated`;

class Movies extends Component {
  constructor(props) {
    super(props);
    this.state = {
      movies: [],
      loading: true,
      error: null,
    };
  }

  componentDidMount() {
    fetch(API_URL)
      .then((res) => {
        if (!res.ok) throw new Error("Failed to fetch movies");
        return res.json();
      })
      .then((data) => {
        // ✅ API returns { current_page, data: [...] } — not "results"
        this.setState({ movies: data.data, loading: false });
      })
      .catch((err) => {
        this.setState({ error: err.message, loading: false });
      });
  }

  render() {
    const { movies, loading, error } = this.state;

    if (loading) {
      return (
        <p style={{ color: "#e94560", textAlign: "center", padding: "40px" }}>
          Loading movies...
        </p>
      );
    }

    if (error) {
      return (
        <p style={{ color: "#e94560", textAlign: "center", padding: "40px" }}>
          Error: {error}
        </p>
      );
    }

    return (
      <div>
        <h2 style={{ color: "#e94560", marginBottom: "20px" }}>
          🎬 Popular Movies
        </h2>
        <div style={{ display: "flex", gap: "16px", flexWrap: "wrap" }}>
          {movies.map((movie) => (
            // ✅ key uses movie.id (UUID string in this API)
            <Movie key={movie.id} {...movie} />
          ))}
        </div>
      </div>
    );
  }
}

export default Movies;