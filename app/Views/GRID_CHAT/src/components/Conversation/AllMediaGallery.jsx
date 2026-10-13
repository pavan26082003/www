import { useState } from "react";
import "./AllMediaGallery.css";

function AllMediaGallery({ contact, messages = [], onBack, onClose, onToast }) {
  const [activeTab, setActiveTab] = useState("media"); // 'media' | 'docs' | 'links'
  const [lightboxItem, setLightboxItem] = useState(null);

  // Extract media items from messages
  const extractedMedia = (messages || [])
    .filter((m) => m.image || m.messageType === "image" || m.messageType === "video")
    .map((m) => ({
      id: m.id,
      url: m.image || "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600&q=80",
      title: m.fileName || m.text || "Shared photo",
      time: m.time || "Recent",
      isVideo: m.messageType === "video",
      duration: m.duration || "0:45",
      period: "recent",
    }));

  // Default rich media dataset for demonstration matching the reference image grid
  const defaultRecentMedia = [
    {
      id: "rm1",
      url: "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=500&q=80",
      title: "UI Design Concept.png",
      period: "recent",
      time: "Today, 11:20 AM",
    },
    {
      id: "rm2",
      url: "https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=500&q=80",
      title: "Analytics Dashboard.jpg",
      period: "recent",
      time: "Today, 10:15 AM",
    },
    {
      id: "rm3",
      url: "https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=500&q=80",
      title: "Brand Mockup.png",
      period: "recent",
      time: "Yesterday, 4:50 PM",
    },
    {
      id: "rm4",
      url: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=500&q=80",
      title: "Marketing Strategy.png",
      period: "recent",
      time: "Yesterday, 3:30 PM",
    },
    {
      id: "rm5",
      url: "https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=500&q=80",
      title: "Team Standup Meeting.jpg",
      period: "recent",
      time: "Yesterday, 2:10 PM",
      isVideo: true,
      duration: "02:14",
    },
    {
      id: "rm6",
      url: "https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=500&q=80",
      title: "Wireframe Overview.jpg",
      period: "recent",
      time: "3 days ago",
    },
  ];

  const defaultLastWeekMedia = [
    {
      id: "lw1",
      url: "https://images.unsplash.com/photo-1557804506-669a67965ba0?w=500&q=80",
      title: "Product Launch Deck.png",
      period: "last_week",
      time: "Sep 3, 2026",
    },
    {
      id: "lw2",
      url: "https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=500&q=80",
      title: "Conference Presentation.jpg",
      period: "last_week",
      time: "Sep 2, 2026",
      isVideo: true,
      duration: "05:32",
    },
    {
      id: "lw3",
      url: "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=500&q=80",
      title: "Workshop Session.jpg",
      period: "last_week",
      time: "Sep 1, 2026",
    },
  ];

  const defaultAugustMedia = [
    {
      id: "ag1",
      url: "https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=500&q=80",
      title: "Project Milestone Review.jpg",
      period: "august",
      time: "Aug 28, 2026",
    },
    {
      id: "ag2",
      url: "https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=500&q=80",
      title: "Developer Documentation.png",
      period: "august",
      time: "Aug 22, 2026",
    },
    {
      id: "ag3",
      url: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&q=80",
      title: "Design System Tokens.jpg",
      period: "august",
      time: "Aug 15, 2026",
    },
  ];

  // Combined media list: message media first, then demo media
  const allMediaList = [...extractedMedia, ...defaultRecentMedia];
  const recentGroup = allMediaList.filter((m) => m.period === "recent");
  const lastWeekGroup = defaultLastWeekMedia;
  const augustGroup = defaultAugustMedia;

  // Documents list extracted from chat + standard docs
  const extractedDocs = (messages || [])
    .filter((m) => m.messageType === "file" || m.messageType === "finished" || m.fileName)
    .map((m) => ({
      id: m.id,
      name: m.fileName || "Document.pdf",
      size: m.fileSize || "2.4 MB",
      type: (m.fileName || "").endsWith(".zip") ? "zip" : "pdf",
      time: m.time || "Recent",
    }));

  const defaultDocs = [
    { id: "d1", name: "Project_Requirements_v2.pdf", size: "4.8 MB", type: "pdf", time: "Today, 10:30 AM" },
    { id: "d2", name: "Style_Guide_Specifications.zip", size: "41.38 MB", type: "zip", time: "Sep 6, 2026" },
    { id: "d3", name: "Sprint_Architecture_Plan.docx", size: "1.2 MB", type: "doc", time: "Sep 2, 2026" },
    { id: "d4", name: "Financial_Report_Q3.xlsx", size: "3.5 MB", type: "sheet", time: "Aug 29, 2026" },
  ];

  const allDocsList = [...extractedDocs, ...defaultDocs];

  // Links list
  const defaultLinks = [
    {
      id: "l1",
      title: "Figma Design Workspace - GRID Chat Application",
      url: "https://figma.com/@grid/whatsapp-application-v2",
      domain: "figma.com",
      time: "Today, 11:15 AM",
    },
    {
      id: "l2",
      title: "GitHub Repository - Frontend Components & State Management",
      url: "https://github.com/grid-projects/chat-application",
      domain: "github.com",
      time: "Sep 5, 2026",
    },
    {
      id: "l3",
      title: "Dribbble Inspiration - Modern Mobile Interaction Patterns",
      url: "https://dribbble.com/shots/chat-ui-interaction-2026",
      domain: "dribbble.com",
      time: "Sep 1, 2026",
    },
    {
      id: "l4",
      title: "FontAwesome Icons v6 Free Library",
      url: "https://fontawesome.com/icons",
      domain: "fontawesome.com",
      time: "Aug 25, 2026",
    },
  ];

  const handleOpenDoc = (doc) => {
    onToast?.(`Opening ${doc.name} (${doc.size})`);
  };

  const handleOpenLink = (link) => {
    onToast?.(`Opening link: ${link.domain}`);
  };

  return (
    <div className="all-media-view">
      {/* Top Header matching Reference 2 */}
      <header className="all-media-header">
        <div className="all-media-header-left">
          <button className="all-media-back-btn" onClick={onBack} title="Back to Contact info">
            <i className="fa-solid fa-arrow-left"></i>
          </button>
          <div className="all-media-title-wrap">
            <h2>Media, links and docs</h2>
            {contact?.name && <span className="all-media-contact-name">{contact.name}</span>}
          </div>
        </div>

        <div className="all-media-header-actions">
          {onClose && (
            <button
              className="all-media-icon-btn close-panel-btn"
              onClick={onClose}
              title="Close"
              aria-label="Close"
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          )}
        </div>
      </header>

      {/* Tabs Row (Media, Docs, Links) */}
      <nav className="all-media-tabs">
        <button
          className={`all-media-tab ${activeTab === "media" ? "active" : ""}`}
          onClick={() => setActiveTab("media")}
        >
          Media
        </button>
        <button
          className={`all-media-tab ${activeTab === "docs" ? "active" : ""}`}
          onClick={() => setActiveTab("docs")}
        >
          Docs
        </button>
        <button
          className={`all-media-tab ${activeTab === "links" ? "active" : ""}`}
          onClick={() => setActiveTab("links")}
        >
          Links
        </button>
      </nav>

      {/* Tab Content Container */}
      <div className="all-media-content">
        {/* ==================== TAB 1: MEDIA ==================== */}
        {activeTab === "media" && (
          <div className="media-tab-content">
            {/* Recent Section */}
            {recentGroup.length > 0 && (
              <div className="media-group-section">
                <div className="media-group-label">RECENT</div>
                <div className="media-grid">
                  {recentGroup.map((item) => (
                    <div
                      key={item.id}
                      className="media-grid-tile"
                      onClick={() => setLightboxItem(item)}
                      title={item.title}
                    >
                      <img src={item.url} alt={item.title} />
                      {item.isVideo && (
                        <div className="video-duration-badge">
                          <i className="fa-solid fa-play"></i> {item.duration}
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Last Week Section */}
            {lastWeekGroup.length > 0 && (
              <div className="media-group-section">
                <div className="media-group-label">LAST WEEK</div>
                <div className="media-grid">
                  {lastWeekGroup.map((item) => (
                    <div
                      key={item.id}
                      className="media-grid-tile"
                      onClick={() => setLightboxItem(item)}
                      title={item.title}
                    >
                      <img src={item.url} alt={item.title} />
                      {item.isVideo && (
                        <div className="video-duration-badge">
                          <i className="fa-solid fa-play"></i> {item.duration}
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* August Section */}
            {augustGroup.length > 0 && (
              <div className="media-group-section">
                <div className="media-group-label">AUGUST</div>
                <div className="media-grid">
                  {augustGroup.map((item) => (
                    <div
                      key={item.id}
                      className="media-grid-tile"
                      onClick={() => setLightboxItem(item)}
                      title={item.title}
                    >
                      <img src={item.url} alt={item.title} />
                      {item.isVideo && (
                        <div className="video-duration-badge">
                          <i className="fa-solid fa-play"></i> {item.duration}
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Reference Image 2: Phone sync notice & View media from all chats */}
            <div className="media-gallery-footer">
              <p className="media-footer-note">
                Use WhatsApp on your phone to see messages from before 12/6/2026.
              </p>
              <button
                type="button"
                className="view-all-chats-btn"
                onClick={() => onToast?.("Viewing media from all chats")}
              >
                <i className="fa-regular fa-image"></i>
                <span>View media from all chats</span>
              </button>
            </div>
          </div>
        )}

        {/* ==================== TAB 2: DOCS ==================== */}
        {activeTab === "docs" && (
          <div className="docs-tab-content">
            <div className="docs-list">
              {allDocsList.map((doc) => (
                <div
                  key={doc.id}
                  className="doc-list-item"
                  onClick={() => handleOpenDoc(doc)}
                >
                  <div className="doc-icon-wrap">
                    {doc.type === "zip" ? (
                      <i className="fa-solid fa-file-zipper doc-icon-zip"></i>
                    ) : doc.type === "sheet" ? (
                      <i className="fa-solid fa-file-excel doc-icon-sheet"></i>
                    ) : doc.type === "doc" ? (
                      <i className="fa-solid fa-file-word doc-icon-word"></i>
                    ) : (
                      <i className="fa-solid fa-file-pdf doc-icon-pdf"></i>
                    )}
                  </div>
                  <div className="doc-details">
                    <h4 className="doc-name">{doc.name}</h4>
                    <p className="doc-meta">
                      <span>{doc.size}</span> • <span>{doc.time}</span>
                    </p>
                  </div>
                  <button
                    className="doc-action-btn"
                    title="Download"
                    onClick={(e) => {
                      e.stopPropagation();
                      onToast?.(`Downloading ${doc.name}`);
                    }}
                  >
                    <i className="fa-solid fa-arrow-down-to-bracket"></i>
                  </button>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ==================== TAB 3: LINKS ==================== */}
        {activeTab === "links" && (
          <div className="links-tab-content">
            <div className="links-list">
              {defaultLinks.map((link) => (
                <div
                  key={link.id}
                  className="link-list-item"
                  onClick={() => handleOpenLink(link)}
                >
                  <div className="link-icon-wrap">
                    <i className="fa-solid fa-link"></i>
                  </div>
                  <div className="link-details">
                    <h4 className="link-title">{link.title}</h4>
                    <a
                      href={link.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="link-url"
                      onClick={(e) => e.stopPropagation()}
                    >
                      {link.url}
                    </a>
                    <span className="link-time">{link.time}</span>
                  </div>
                  <button
                    className="link-action-btn"
                    title="Open link"
                    onClick={() => handleOpenLink(link)}
                  >
                    <i className="fa-solid fa-arrow-up-right-from-square"></i>
                  </button>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>

      {/* Lightbox Modal for full image preview */}
      {lightboxItem && (
        <div className="media-lightbox-overlay" onClick={() => setLightboxItem(null)}>
          <div className="media-lightbox-container" onClick={(e) => e.stopPropagation()}>
            <header className="lightbox-header">
              <div className="lightbox-info">
                <h3>{lightboxItem.title}</h3>
                <small>{lightboxItem.time}</small>
              </div>
              <div className="lightbox-actions">
                <button
                  className="lightbox-btn"
                  title="Download"
                  onClick={() => onToast?.(`Downloading ${lightboxItem.title}`)}
                >
                  <i className="fa-solid fa-download"></i>
                </button>
                <button
                  className="lightbox-btn"
                  title="Close"
                  onClick={() => setLightboxItem(null)}
                >
                  <i className="fa-solid fa-xmark"></i>
                </button>
              </div>
            </header>

            <div className="lightbox-body">
              <img src={lightboxItem.url} alt={lightboxItem.title} />
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default AllMediaGallery;
