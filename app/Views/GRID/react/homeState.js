/**
 * GRID Home Workspace State Store
 * Centralized data contract for Disciplines, Suggested Topics, Feed Posts, Visiting Card, and Network stats.
 */

export const initialHomeState = {
    currentUser: {
        id: '1',
        name: 'Praful Shah',
        firstName: 'Praful',
        role: 'Update your profile',
        avatar: '/assets/images/avatar.jpg',
        skillEnds: 4,
        gridId: 'PS8131-F9-2026'
    },
    disciplines: [
        { id: 'graphic', label: 'Graphic Designer', icon: 'draw' },
        { id: 'web-design', label: 'Web Designer', icon: 'web' },
        { id: 'figma', label: 'Figma', icon: 'token', isBrand: true },
        { id: 'web-dev', label: 'Web Developer', icon: 'code' },
        { id: 'ui-ux', label: 'UI Designer', icon: 'design_services' }
    ],
    suggestedTopics: [
        {
            id: 'topic-1',
            title: 'Brand Design',
            icon: 'palette',
            description: "The best brand designers and identity architects are on Grid. Explore design systems, typography & brand books.",
            stat1Val: '23K',
            stat1Label: 'Contributors',
            stat2Val: '36K',
            stat2Label: 'Posts',
            bgImage: 'https://images.unsplash.com/photo-1600132806370-bf17e65e942f?w=600&auto=format&fit=crop&q=80',
            avatars: [
                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&auto=format&fit=crop&q=80'
            ]
        },
        {
            id: 'topic-2',
            title: 'Midjourney',
            icon: 'sailing',
            description: 'Generate incredible visual concepts and assets with prompt workflows. Reflect on novel generative pipelines...',
            stat1Val: '6.4K',
            stat1Label: 'Contributors',
            stat2Val: '4.4K',
            stat2Label: 'Posts',
            bgImage: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600&auto=format&fit=crop&q=80',
            avatars: [
                'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80'
            ],
            extraBadge: '+4'
        },
        {
            id: 'topic-3',
            title: 'Flora Museum Challenge',
            iconType: 'flora',
            description: 'Submit your creation for a chance to win',
            stat1Val: '$10K',
            stat1Label: 'Prize',
            stat2Val: 'Today',
            stat2Label: 'Ended',
            bgImage: 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?w=600&auto=format&fit=crop&q=80',
            avatars: [
                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80'
            ]
        },
        {
            id: 'topic-4',
            title: 'Descript Challenge',
            iconType: 'descript',
            description: 'Make a video that teaches one thing from your workflow. In Descript. $5,000 in prizes...',
            stat1Val: '$5K',
            stat1Label: 'Prize',
            stat2Val: '18h 40m',
            stat2Label: 'Left',
            bgImage: 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=600&auto=format&fit=crop&q=80',
            avatars: [
                'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=100&auto=format&fit=crop&q=80'
            ]
        },
        {
            id: 'topic-5',
            title: 'CapCut Design',
            iconType: 'capcut',
            description: 'Available to U.S. designers. Showcase short-form motion templates & generative cuts.',
            stat1Val: '$11K',
            stat1Label: 'Prize',
            stat2Val: '17d',
            stat2Label: 'Left',
            isLight: true,
            bgGradient: 'bg-gradient-to-tr from-sky-400 via-cyan-200 to-white',
            avatars: [
                'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80',
                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80'
            ]
        }
    ],
    posts: [
        {
            id: 'post-nikhil',
            type: 'experience_share',
            author: {
                name: 'Nikhil Singh',
                degree: '3rd+',
                role: "CSE'26 | Java | DSA | MERN Stack | Postman | HTML | CSS | JAVASCRIPT",
                avatar: 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&auto=format&fit=crop&q=80',
                timeAgo: '1d • Edited',
                verified: false,
                discipline: 'Software Engineer'
            },
            truncateAfter: 'The assessment consisted of three stages:',
            content: `I want to share a disappointing experience I had during the recruitment assessment conducted by Genesis Technologies India.
The assessment consisted of three stages:
Aptitude — a qualifying section required to proceed further
Topic Writing
Programming
I successfully cleared the aptitude section and was allowed to proceed to the topic-writing section. I completed the writing task and was then moved to the programming section.
In the programming section, I completed the given problem and the program produced the exact expected output. With around 5 minutes remaining before the assessment was scheduled to end, I chose to finish and submit the test.

However, after clicking the finish option, I received a pop up stating:
"Unusual activity detected. Test debarred. Contact HR."

I immediately contacted the HR representative and explained what had happened. I was told that since I was simply submitting the test, I should click "OK" and proceed. Shortly afterward, I received an email stating that my test had been successfully submitted and that the results would be communicated within 2–3 days.

I considered the matter resolved and left.
Around 15 minutes later, I received a rejection/regret email stating that I had not cleared the round.
I immediately contacted HR to understand what had happened. I was told that I had not cleared both the aptitude and programming sections.

Now I don't understand why my test was not ended immediately if I didn't cleared the aptitude section, and why my code after giving exact output needed was considered wrong.
I guess people don't know how to take or check Papers.

So, I just want to say that if you intend to only waste time of candidates don't even bother post hiring opportunities, Hiring is not some way to increase publicity of your Company.

#genesistechnologies #hiring #hr #indore #walkin #CandidateExperience #CampusHiring #JobSearch #Transparency`,
            mediaCard: {
                recipient: 'Nikhil Pratap Singh,',
                message: 'We regret to inform you that you could not qualify our online screening test conducted on 2026/09/09. Thank you for showing interest in Genesis Technologies, and we appreciate the time and effort you invested in the process.',
                closing: 'We wish you all the best for your future endeavors.',
                quotedText: 'Show quoted text'
            },
            metrics: {
                likes: 55,
                comments: 5,
                reposts: 2
            }
        },
        {
            id: 'post-1',
            type: 'tech_artifact',
            author: {
                name: 'Elena Rostova',
                role: 'Staff Product Designer at Linear Labs • Followed by 14k',
                avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
                timeAgo: '1h ago',
                verified: true,
                discipline: 'UI Designer'
            },
            content: `We just open-sourced the v3 interaction design and execution spec for Canvas Shaders. After 4 months of rigorous bench testing with 60fps WebGL nodes, here is our full engineering breakdown on memory isolation, cross-context canvas blending, and GPU draw-call batching across dense node networks.

Our benchmark harness systematically tests GPU state isolation between WebGL canvases and React Three Fiber trees. Check out the GitHub repository below for full documentation and reproducible benchmarks.

#webgl #reactthreefiber #computeshaders #opensource #graphics`,
            tags: ['#WebGL', '#ReactThreeFiber', '#OpenSource', '#ComputeShaders'],
            artifact: {
                badge: 'OPEN SOURCE REPO',
                version: 'v3.0.2',
                title: 'Canvas Nodes Architecture',
                description: '18 Production Components • Modular React Three Fiber Primitives • Memory Isolated GPU Pipelines.',
                image: 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=600&auto=format&fit=crop&q=80',
                tags: ['DPO', 'WebGL', 'PyTorch', 'vLLM'],
                repoUrl: 'github.com/linearlabs/canvas',
                stars: 142
            },
            metrics: {
                likes: 142,
                comments: 28,
                reposts: 14
            }
        },
        {
            id: 'post-2',
            type: 'opportunity',
            author: {
                name: 'Marcus Vance',
                role: 'Founder & CEO at Kinetic AI • Hiring Founding Core',
                avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80',
                timeAgo: '3h ago',
                verified: true,
                discipline: 'Web Developer'
            },
            tags: ['#Hiring', '#SystemsDesign', '#KineticAI', '#Jobs'],
            job: {
                badge: 'EXP',
                title: 'Founding Systems Designer',
                type: 'Full-time',
                company: 'Kinetic AI',
                track: 'Lead Product Architecture',
                description: 'We are architecting an autonomous agent orchestration workspace. You will lead design systems engineering and collaborate directly alongside our founding AI core to construct multimodal generative canvases.',
                location: 'Remote (US / EU)',
                compensation: 'Immediate Start',
                funding: 'Series A ($14M Lead)'
            },
            metrics: {
                likes: 89,
                comments: 16,
                reposts: 9
            }
        },
        {
            id: 'post-3',
            type: 'marketplace',
            author: {
                name: 'Sarah Chen',
                role: 'Principal Brand Architect • Studio Service Listing',
                avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80',
                timeAgo: '5h ago',
                verified: true,
                discipline: 'Graphic Designer'
            },
            tags: ['#BrandDesign', '#Branding', '#BrandDesigner', '#DesignSystems'],
            content: 'Opening 2 advisory slots for late Q2. If your engineering and product teams are struggling with design token drift between Figma and production React/Tailwind codebases, this package delivers deterministic parity.\n\n#BrandDesign #DesignSystems #Branding #BrandDesigner',
            service: {
                title: 'Design System Token Audit & Pipeline Migration',
                price: 'Starting at $2,400 USD',
                turnaround: '5 Business Days',
                deliverables: 'Style Dictionary & CI setup'
            },
            metrics: {
                likes: 52,
                comments: 8,
                reposts: 4
            }
        }
    ],
    visitingCard: {
        owner: 'K Pavan Kumar',
        cardId: '#GRID-PK-2025'
    },
    onlineUsers: {
        onlineCount: 1211,
        activeInNetwork: 3,
        pendingConnections: 0,
        activeAvatars: [
            { initials: 'AK', bg: 'bg-slate-900', text: 'text-white' },
            { initials: 'SK', bg: 'bg-blue-600', text: 'text-white' },
            { initials: 'AT', bg: 'bg-emerald-700', text: 'text-white' }
        ]
    }
};

/**
 * Returns a cloned initial state for the Home Workspace, dynamically bound to the active user
 */
export function getHomeInitialState() {
    const state = JSON.parse(JSON.stringify(initialHomeState));
    if (typeof window !== 'undefined' && window.__GRID_DATA__ && window.__GRID_DATA__.user) {
        const u = window.__GRID_DATA__.user;
        if (u.id) state.currentUser.id = String(u.id);
        if (u.name) state.currentUser.name = u.name;
        if (u.firstName) state.currentUser.firstName = u.firstName;
        if (u.headline) state.currentUser.role = u.headline;
        if (u.avatar) state.currentUser.avatar = u.avatar;
        if (u.gridId) state.currentUser.gridId = u.gridId;
        if (u.name) state.visitingCard.owner = u.name;
        if (u.gridId) state.visitingCard.cardId = `#GRID-${u.gridId}`;
    }
    return state;
}
