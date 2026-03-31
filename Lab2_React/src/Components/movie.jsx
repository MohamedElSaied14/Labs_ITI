import { Component } from "react";

class Movie extends Component {
  render() {
    const {
      original_title,   // ✅ this API uses original_title, not title
      vote_average,
      poster_path,      // ✅ already a full URL in this API
      overview,
      release_date,
      casts = [],       // ✅ bonus: this API includes cast data
    } = this.props;

    const topCast = casts
      .sort((a, b) => b.popularity - a.popularity)
      .slice(0, 3)
      .map((c) => c.name)
      .join(", ");

    return (
      <div
        style={{
          background: "#1a1a2e",
          border: "1px solid #e94560",
          borderRadius: "12px",
          overflow: "hidden",
          width: "200px",
          flexShrink: 0,
          boxShadow: "0 4px 20px rgba(233,69,96,0.2)",
          transition: "transform 0.2s",
        }}
        onMouseEnter={(e) =>
          (e.currentTarget.style.transform = "translateY(-4px)")
        }
        onMouseLeave={(e) => (e.currentTarget.style.transform = "translateY(0)")}
      >
        <img
          src={poster_path || "https://via.placeholder.com/300x450?text=No+Image"}
          alt={original_title}
          style={{ width: "100%", height: "280px", objectFit: "cover" }}
        />
        <div style={{ padding: "12px" }}>
          <h3
            style={{
              color: "#e94560",
              margin: "0 0 6px",
              fontSize: "14px",
              fontWeight: 700,
            }}
          >
            {original_title}
          </h3>
          <p style={{ color: "#f5f5f5", fontSize: "12px", margin: "0 0 4px" }}>
            ⭐ {Number(vote_average).toFixed(1)} / 10
          </p>
          <p style={{ color: "#888", fontSize: "11px", margin: "0 0 6px" }}>
            {release_date}
          </p>
          <p
            style={{
              color: "#aaa",
              fontSize: "11px",
              margin: "0 0 6px",
              lineHeight: 1.4,
              display: "-webkit-box",
              WebkitLineClamp: 3,
              WebkitBoxOrient: "vertical",
              overflow: "hidden",
            }}
          >
            {overview}
          </p>
          {topCast && (
            <p style={{ color: "#666", fontSize: "10px", margin: 0 }}>
              🎭 {topCast}
            </p>
          )}
        </div>
      </div>
    );
  }
}

export default Movie;