/**
 * networkState.js
 * Centralized data contract for GRID Network & Verified Peer Graph (eFactor Network).
 * Synchronized with Stitch reference project "LinkedIn Style Network Hub".
 */

export const initialNetworkState = {
    manageNetworkItems: [
        { id: 'connections', label: 'Connections', count: '1,428', icon: 'connections' },
        { id: 'following', label: 'Following & followers', count: '3,890', icon: 'following' },
        { id: 'events', label: 'Events', count: '3', icon: 'events' },
        { id: 'pages', label: 'Pages', count: '74', icon: 'pages' },
        { id: 'newsletters', label: 'Newsletters', count: '10', icon: 'newsletters' },
        { id: 'hashtags', label: 'Hashtags', count: '18', icon: 'hashtags' }
    ],
    topicChips: [
        { id: 'all', label: 'All Recommendations' },
        { id: 'ai', label: '✨ AI & LLM Specialists' },
        { id: 'design', label: 'Product Designers' },
        { id: 'founders', label: 'Founders & Executives' },
        { id: 'hub', label: 'Bengaluru / Hyderabad Tech Hub' }
    ],
    pendingInvitations: [
        {
            id: 'inv-1',
            name: 'Kavya Sundaram',
            tag: '1st Mutual',
            headline: 'Staff AI Research Engineer at Applied Labs • Former CMU',
            avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=256&q=80',
            mutualCount: 15,
            mutualText: 'Elena Rostova and 14 other mutual connections',
            isOnline: true
        }
    ],
    recentActivityPeers: [
        {
            id: 'peer-1',
            name: 'Sai Teja Anangi',
            headline: 'Full-Stack Developer | Ex-Intern @Spotmies | Web3 Lead',
            avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=256&q=80',
            bannerGradient: 'from-blue-600 via-indigo-600 to-cyan-500',
            isOnline: true,
            verified: true,
            discipline: 'ai',
            mutualCount: 16,
            mutualText: 'Adarsh and 15 other mutuals',
            mutualAvatars: [
                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=64&q=80',
                'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=64&q=80'
            ]
        },
        {
            id: 'peer-2',
            name: 'Harshini Patti',
            headline: 'Systems Engineer @TCS | Cloud Infrastructure Enthusiast',
            avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=256&q=80',
            bannerGradient: 'from-emerald-600 to-teal-700',
            isOnline: false,
            verified: true,
            discipline: 'engineers',
            mutualCount: 1,
            mutualText: 'Bhargava Sai is a mutual',
            mutualAvatars: [
                'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=64&q=80'
            ]
        },
        {
            id: 'peer-3',
            name: 'Yashwanth Reddy',
            headline: 'Senior Software Architect | High Scale Distributed Systems',
            avatar: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=256&q=80',
            bannerGradient: 'from-violet-600 to-purple-800',
            isOnline: true,
            verified: true,
            discipline: 'engineers',
            mutualCount: 10,
            mutualText: 'Kondapaneni and 9 others',
            mutualAvatars: [
                'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=64&q=80',
                'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=64&q=80'
            ]
        },
        {
            id: 'peer-4',
            name: 'Shleshitha Cheekati',
            headline: 'AI Specialist @TCS | B.Tech CSE (AI & ML)',
            avatar: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=256&q=80',
            bannerGradient: 'from-amber-600 to-rose-600',
            isOnline: false,
            verified: true,
            discipline: 'ai',
            mutualCount: 1,
            mutualText: 'Bhargava Sai is a mutual',
            mutualAvatars: [
                'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=64&q=80'
            ]
        }
    ],
    trendingHubPeers: [
        {
            id: 'peer-5',
            name: 'Dr. P. Revanth',
            headline: 'Assistant Professor • AI & Machine Learning Research',
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80',
            bannerGradient: 'from-teal-700 to-emerald-800',
            isOpenToWork: true,
            verified: true,
            discipline: 'ai',
            mutualCount: 42,
            mutualText: 'Sampath & 41 other mutuals',
            mutualAvatars: [
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=64&q=80',
                'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=64&q=80'
            ]
        },
        {
            id: 'peer-6',
            name: 'AI with KUMAR',
            headline: 'AI News, LLM Architecture & Enterprise Agents',
            avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=256&q=80',
            bannerGradient: 'from-blue-900 to-indigo-900',
            isFollowable: true,
            verified: false,
            discipline: 'ai',
            followersCount: '12,400+ followers',
            mutualText: '12,400+ followers'
        },
        {
            id: 'peer-7',
            name: 'Bhavana Pentum',
            headline: 'Agentic AI Engineer | GenAI | Fine-Tuning LLMs',
            avatar: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=256&q=80',
            bannerGradient: 'from-cyan-600 to-blue-700',
            isOpenToWork: true,
            verified: true,
            discipline: 'ai',
            mutualCount: 8,
            mutualText: 'Srujan and 7 mutuals',
            mutualAvatars: [
                'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=64&q=80'
            ]
        },
        {
            id: 'peer-8',
            name: 'Srujith Bollam',
            headline: 'Applied AI/ML Engineer | LangGraph, RAG & VectorDBs',
            avatar: 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=256&q=80',
            bannerGradient: 'from-slate-700 to-slate-900',
            isOpenToWork: true,
            verified: true,
            discipline: 'ai',
            mutualCount: 27,
            mutualText: 'Akhil & 26 other mutuals',
            mutualAvatars: [
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=64&q=80'
            ]
        }
    ],
    newsletters: [
        {
            id: 'news-1',
            iconText: '#AI',
            title: 'Canvas Nodes & Shader Weekly',
            cadence: 'Weekly',
            author: 'Elena Rostova',
            authorRole: 'Staff Product Designer at Linear Labs',
            description: 'Deep dive into 60fps WebGL shaders and GPU-accelerated canvas components.',
            stats: '14,200 subscribers • 4.9 ★',
            schedule: 'Published every Wednesday'
        },
        {
            id: 'news-2',
            iconText: '⚡️',
            title: 'Design Tokens & Scaled Systems',
            cadence: 'Bi-weekly',
            author: 'Sarah Chen',
            authorRole: 'Principal Brand Architect',
            description: 'Bridging Figma tokens with deterministic Tailwind & React production systems.',
            stats: '8,830 subscribers • 127 editions',
            schedule: 'Published every other Friday'
        }
    ],
    subscribedSpaces: [
        { name: 'AI Post-Training & Alignment', members: '1,420 peers', tag: 'ML' },
        { name: 'Design Systems Guild', members: '890 peers', tag: 'UI' },
        { name: 'Next.js & React Core', members: '2,100 peers', tag: 'JS' },
        { name: 'Silicon Valley Founders', members: '340 peers', tag: 'VC' }
    ],
    recommendations: [
        {
            id: 'rec-1',
            name: 'Arjun Nambiar',
            headline: 'Lead AI Engineer at HyperScale Systems',
            avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=256&q=80',
            reason: 'Worked on PyTorch Transformers'
        },
        {
            id: 'rec-2',
            name: 'Priya Sharma',
            headline: 'Design Systems Architect at Lattice Labs',
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80',
            reason: 'Shared 14 mutual engineering peers'
        },
        {
            id: 'rec-3',
            name: 'Vikram Joshi',
            headline: 'Founder @ KernelOps • YC W24',
            avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=256&q=80',
            reason: 'Follows AI & Systems Engineering Hub'
        }
    ],
    connectionsList: [
        {
            id: 'conn-1',
            name: 'Abijar Machakaire',
            headline: 'Full Stack Website Developer | CAD House Plan Designer | Wordpress Developer |Web & Mobile Designer | UI/UX | Graphic Design',
            avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 15, 2026',
            isOpenToWork: true
        },
        {
            id: 'conn-2',
            name: 'Gandhey Vignesh',
            headline: 'AI Intern @TheSmartBridge | B.Tech Final Year @MallaReddyUniversity | Python | AI/ML | AWS | UI/UX Designer | SQL | React | Data Analytics',
            avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 10, 2026',
            isOpenToWork: false
        },
        {
            id: 'conn-3',
            name: 'Vishwanath Goud',
            headline: 'AI & Data Science Student | Python | Numpy | Pandas',
            avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 8, 2026',
            isOpenToWork: false
        },
        {
            id: 'conn-4',
            name: 'POLEPALLI LAVANYA',
            headline: 'B.Tech Student | AI & ML Enthusiast | Skilled in Python | Aspiring ML Engineer',
            avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 8, 2026',
            isOpenToWork: false
        },
        {
            id: 'conn-5',
            name: 'Rajul Goel',
            headline: 'LLM Post Training Intern at Ethara AI | Aspiring Data Analyst | Python, SQL, NLP, Data Visualization',
            avatar: 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 7, 2026',
            isOpenToWork: false
        },
        {
            id: 'conn-6',
            name: 'Sanjay Bandaru',
            headline: 'Infosys Springboard Intern | Aspiring AI & Machine Learning Engineer | B.Tech AIML Student | Passionate About Data Science, Generative AI & Intelligent Solutions',
            avatar: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 7, 2026',
            isOpenToWork: true
        },
        {
            id: 'conn-7',
            name: 'Arutla Sharath reddy',
            headline: 'Data Science Student at Malla Reddy University | Aspiring Data Analyst | Python | SQL | Machine Learning | AI Enthusiast',
            avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 7, 2026',
            isOpenToWork: true
        },
        {
            id: 'conn-8',
            name: 'Bala Rakesh Reddy Vatti',
            headline: 'AI Consultant | Helping Students & Professionals Build Real-World AI Applications | Generative AI • LLMs • RAG • Computer Vision | Matricz AI',
            avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 7, 2026',
            isOpenToWork: false
        },
        {
            id: 'conn-9',
            name: 'Ceelasagaram Shyam Sunder',
            headline: 'Engineering student | Future innovator | passionate about protecting systems and innovating in cyber defence| Malla Reddy College of Engineering and Technology.',
            avatar: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=256&q=80',
            connectedDate: 'September 7, 2026',
            isOpenToWork: false
        },
        {
            id: 'conn-10',
            name: 'Charvitha Lingala',
            headline: 'CSE Student | Passionate About Technology and Innovation',
            avatar: '',
            initials: 'CL',
            connectedDate: 'September 4, 2026',
            isOpenToWork: false
        },
        {
            id: 'conn-11',
            name: 'Kumbala Rishith Reddy',
            headline: 'Data Science Student at Malla Reddy University Hyderabad | Aspiring Data Analyst | Python | SQL | Machine Learning | AI Enthusiast',
            avatar: '',
            initials: 'KR',
            connectedDate: 'September 3, 2026',
            isOpenToWork: false
        }
    ],
    eventsList: [
        {
            id: 'ev-1',
            title: 'Global AI & Python Systems Engineering Summit 2026',
            date: 'Thu, Sep 24 • 6:30 PM IST',
            type: 'Online Webinar',
            attendees: '1,840 attendees',
            coverGradient: 'from-blue-600 via-indigo-600 to-purple-600',
            organizer: 'GRID AI Guild'
        },
        {
            id: 'ev-2',
            title: 'Design Systems & Glassmorphism UI Architecture Workshop',
            date: 'Sat, Oct 03 • 4:00 PM IST',
            type: 'Virtual Masterclass',
            attendees: '920 designers',
            coverGradient: 'from-cyan-500 via-blue-600 to-indigo-700',
            organizer: 'Frontend Leaders Collective'
        },
        {
            id: 'ev-3',
            title: 'Hyderabad Tech Founders & AI Startup Mixer',
            date: 'Fri, Oct 16 • 7:00 PM IST',
            type: 'In-person • Hitec City',
            attendees: '340 founders',
            coverGradient: 'from-emerald-600 to-teal-700',
            organizer: 'Telangana Tech Hub'
        }
    ],
    pagesList: [
        {
            id: 'page-1',
            name: 'Novatech Global Solutions',
            tagline: 'Enterprise Cloud Automation & Next-Gen AI Platforms',
            followers: '142K followers',
            industry: 'Software Development',
            logoBg: '#2563eb',
            logoText: 'NT'
        },
        {
            id: 'page-2',
            name: 'Malla Reddy University',
            tagline: 'Center of Excellence for AI, Machine Learning & Robotics',
            followers: '48K followers',
            industry: 'Higher Education',
            logoBg: '#059669',
            logoText: 'MRU'
        },
        {
            id: 'page-3',
            name: 'Google DeepMind',
            tagline: 'Solving intelligence to advance science and benefit humanity',
            followers: '1.8M followers',
            industry: 'Artificial Intelligence',
            logoBg: '#1e293b',
            logoText: 'DM'
        },
        {
            id: 'page-4',
            name: 'Kinetic AI Labs',
            tagline: 'Autonomous AI Agents & Real-Time Foundation Models',
            followers: '84K followers',
            industry: 'Artificial Intelligence',
            logoBg: '#7c3aed',
            logoText: 'K'
        }
    ]
};

export function getInitialNetworkState() {
    return JSON.parse(JSON.stringify(initialNetworkState));
}
