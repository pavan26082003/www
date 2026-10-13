import "./Header.css";

function Header() {
  return (
    <header className="common-header">
      <div className="header-left">
        <div className="grid-logo">
          GRID
        </div>
      </div>

      <div className="header-center">
        <span>MyChat</span>
      </div>

      <div className="header-right">
        <button className="header-icon-btn" title="Search">
          🔍
        </button>

        <button className="header-icon-btn" title="Notifications">
          🔔
        </button>

        <button className="header-profile">
          <img
            src="https://i.pravatar.cc/100?img=12"
            alt="Profile"
          />
        </button>
      </div>
    </header>
  );
}

export default Header;