import { Component } from "react";

class Task extends Component {
    constructor(props) {
        super()
        this.state = {name: "React", age: 46}
    }
    render() {
        return <h1>Task of {this.state.name}  {this.state.age} - {this.props.taskName}  </h1>

    }
}

export default Task;