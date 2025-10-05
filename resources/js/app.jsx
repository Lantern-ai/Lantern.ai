
import ScreenplayEditor from './components/ScreenplayEditor/ScreenplayEditor';

function App() {
    const el = document.getElementById('react-root');
    const props = el.dataset.props ? JSON.parse(el.dataset.props) : {};
console.log(props);
console.log(props.content);
    return (
        <ScreenplayEditor {...props}/>
    );
}

export default App;
