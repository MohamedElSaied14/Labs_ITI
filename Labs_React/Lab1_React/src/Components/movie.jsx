const Movie =(props)=>{
    console.log(props);
    let {name, rate} = props;
    return(
        <div style={{backgroundColor:"brown" , margin:10 ,padding:10}}>
            <h1>Movie Component</h1>
            <p>Movie Name: {name}</p>
            <p>Movie Rating: {rate}</p>
        </div>
    )
}

export default Movie;