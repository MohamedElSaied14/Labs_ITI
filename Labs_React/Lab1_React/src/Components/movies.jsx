import { useState } from "react"
import Movie from "./movie";

const Movies = () => {
    var [arr] = useState([
        { rate: 1, name: "daredevil" },
        { rate: 2, name: "spiderman" },
        { rate: 3, name: "Batman" }
    ])
        console.log(arr);
    return (
        <div>
            {arr.map(u=>{
                 return <Movie {...u}></Movie>
            })}
        </div>

    );
}

export default Movies;
