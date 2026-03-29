// import { Fragment } from "react"
import Task from "./Components/task"
import User from "./Components/movie"
import Movie from "./Components/movie"
import Movies from "./Components/movies"
function App() {

  var name = "OS"
  // console.log(name)
  var objstyle = { backgroundColor: "red", color: "white", margin: "20px" }
  // "background-color:red ; color:white ; margin: 20px;"
  return (
    <>
      <Task taskName={name}></Task>
      <Movies></Movies>

    </>

    // <h1 className="c1">hello ya {name.toLocaleLowerCase()}</h1>
    // <div>
    // <Fragment>
    // <>
    //   <h1 style={{ backgroundColor: "red", color: "white", margin: "20px" }
    //     < label htmlFor="name">Name: </label>
    //   <input id="name"></input>
    // </>
    // </Fragment>
    // </div>

  )

}

export default App