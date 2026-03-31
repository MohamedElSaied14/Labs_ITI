import { Component } from "react";

class Task extends Component {
  constructor(props) {
    super(props);
    this.state = { name: "React", age: 46 };
  }

  render() {
    return (
      <h1 style={{ color: "#e94560" }}>
        Task of {this.state.name} {this.state.age} - {this.props.taskName}
      </h1>
    );
  }
}

export default Task;