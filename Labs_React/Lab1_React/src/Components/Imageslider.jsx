import { Component } from "react";

// 📁 Put your images in: public/slider/ OR src/assets/slider/
// Then update the paths array below accordingly.
// Example with public folder (recommended for React):
//   "/slider/image1.jpg"  → file lives at public/slider/image1.jpg

const SLIDES = [
  {
    src: "../src/assets/slide1.jpg",
    caption: "Adventure Awaits",
  },
  {
    src: "../src/assets/slide2.jpg",
    caption: "Epic Stories",
  },
  {
    src: "../src/assets/slide3.jpg",
    caption: "Unforgettable Moments",
  },
];

class ImageSlider extends Component {
  constructor(props) {
    super(props);
    this.state = {
      currentIndex: 0,
    };
    // Bind handlers so 'this' works inside them
    this.handleNext = this.handleNext.bind(this);
    this.handlePrev = this.handlePrev.bind(this);
  }

  handleNext() {
    this.setState((prevState) => ({
      currentIndex: (prevState.currentIndex + 1) % SLIDES.length,
    }));
  }

  handlePrev() {
    this.setState((prevState) => ({
      currentIndex:
        (prevState.currentIndex - 1 + SLIDES.length) % SLIDES.length,
    }));
  }

  render() {
    const { currentIndex } = this.state;
    const slide = SLIDES[currentIndex];

    const containerStyle = {
      position: "relative",
      width: "100%",
      maxWidth: "720px",
      margin: "0 auto",
      borderRadius: "16px",
      overflow: "hidden",
      background: "#0f0f1a",
      boxShadow: "0 8px 40px rgba(0,0,0,0.5)",
    };

    const imgStyle = {
      width: "100%",
      height: "400px",
      objectFit: "cover",
      display: "block",
    };

    const overlayStyle = {
      position: "absolute",
      bottom: 0,
      left: 0,
      right: 0,
      padding: "20px 24px",
      background: "linear-gradient(transparent, rgba(0,0,0,0.75))",
      display: "flex",
      justifyContent: "space-between",
      alignItems: "center",
    };

    const btnStyle = {
      background: "#e94560",
      color: "#fff",
      border: "none",
      borderRadius: "8px",
      padding: "10px 20px",
      fontSize: "18px",
      cursor: "pointer",
      fontWeight: 700,
      transition: "background 0.2s",
    };

    const captionStyle = {
      color: "#fff",
      fontSize: "18px",
      fontWeight: 600,
      textShadow: "0 2px 8px rgba(0,0,0,0.8)",
    };

    const dotsStyle = {
      display: "flex",
      justifyContent: "center",
      gap: "8px",
      marginTop: "12px",
    };

    return (
      <div>
        <div style={containerStyle}>
          <img src={slide.src} alt={slide.caption} style={imgStyle} />
          <div style={overlayStyle}>
            <button
              style={btnStyle}
              onClick={this.handlePrev}
              onMouseEnter={(e) => (e.currentTarget.style.background = "#c73652")}
              onMouseLeave={(e) => (e.currentTarget.style.background = "#e94560")}
            >
              ‹ Prev
            </button>
            <span style={captionStyle}>{slide.caption}</span>
            <button
              style={btnStyle}
              onClick={this.handleNext}
              onMouseEnter={(e) => (e.currentTarget.style.background = "#c73652")}
              onMouseLeave={(e) => (e.currentTarget.style.background = "#e94560")}
            >
              Next ›
            </button>
          </div>
        </div>

        {/* Dot indicators */}
        <div style={dotsStyle}>
          {SLIDES.map((_, i) => (
            <span
              key={i}
              style={{
                width: "10px",
                height: "10px",
                borderRadius: "50%",
                background: i === currentIndex ? "#e94560" : "#555",
                cursor: "pointer",
                transition: "background 0.2s",
              }}
              onClick={() => this.setState({ currentIndex: i })}
            />
          ))}
        </div>

        {/* Counter */}
        <p style={{ textAlign: "center", color: "#888", marginTop: "8px", fontSize: "13px" }}>
          {currentIndex + 1} / {SLIDES.length}
        </p>
      </div>
    );
  }
}

export default ImageSlider;