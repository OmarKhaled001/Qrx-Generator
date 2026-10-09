// Landing hero: the phone scans the printed code and the code turns into content.
// The server renders the finished frame; this timeline starts from the first frame (fromTo/from
// render their start state immediately). Only transforms and opacity are animated, so there is no
// layout work. Loaded only when the visitor has not asked for reduced motion (see app.js). RTL is
// handled by the wrapper's CSS mirror, so the coordinates here are the same in both directions.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const BRAND_400 = '#2ec3c6';

export function playHero(scene) {
    const hero = scene.closest('[data-hero]');
    const part = (name) => scene.querySelectorAll(`[data-part="${name}"]`);
    const header = document.querySelector('body > header');
    const center = { transformOrigin: '50% 50%' };

    function timeline({ particles = true, chips = true } = {}) {
        const tl = gsap.timeline({ defaults: { ease: 'power2.out' } });

        // 1. The phone swings in; the camera view (opaque, it hides the content) finds the code.
        tl.from(part('phone'), { x: 80, y: 24, rotation: 10, ...center, duration: 2 }, 0)
            .fromTo(part('camera'), { opacity: 1 }, { opacity: 0, duration: 0.8 }, 4)
            .fromTo(part('finder'), { opacity: 0, scale: 1.3, ...center }, { opacity: 1, scale: 1, duration: 1.4 }, 0.5)
            .fromTo(part('brackets'), { opacity: 0, scale: 1.25, stroke: '#ffffff', ...center }, { opacity: 1, scale: 1, duration: 1.2 }, 0.8);

        // 2. A scan line sweeps across, the corners lock on and the printed code pings.
        tl.fromTo(part('scan'), { opacity: 0, y: 0 }, { opacity: 1, duration: 0.3 }, 2)
            .to(part('scan'), { y: 128, duration: 1.2, ease: 'none' }, 2.1)
            .to(part('scan'), { opacity: 0, duration: 0.3 }, 3.2)
            .to(part('brackets'), { stroke: BRAND_400, duration: 0.3 }, 3.1)
            .fromTo(part('ping'), { opacity: 0, scale: 0.7, ...center }, { opacity: 0.9, duration: 0.15 }, 3)
            .to(part('ping'), { opacity: 0, scale: 1.25, duration: 1.1, ease: 'power1.out' }, 3.15);

        // 3. The code breaks apart.
        tl.to(part('finder'), { opacity: 0, scale: 0.85, duration: 0.9 }, 3.6).to(part('brackets'), { opacity: 0, scale: 1.1, duration: 0.6 }, 3.6);
        if (particles) {
            part('particle').forEach((el) => {
                tl.fromTo(el, { opacity: 0, x: 0, y: 0, rotation: 0 }, { opacity: 1, duration: 0.2 }, 3.6).to(
                    el,
                    { x: Number(el.dataset.dx), y: Number(el.dataset.dy), rotation: gsap.utils.random(-90, 90), opacity: 0, ...center, duration: 1.4, ease: 'power3.out' },
                    3.7,
                );
            });
        }

        // 4. The content builds up as the camera view fades.
        tl.from(part('header'), { y: -24, opacity: 0, duration: 0.8, stagger: 0.05 }, 4.2)
            .from(part('row'), { y: 18, opacity: 0, duration: 0.7, stagger: 0.2 }, 4.6)
            .from(part('cta'), { scale: 0.9, opacity: 0, ...center, duration: 0.6, ease: 'back.out(2)' }, 5.3);

        // 5. Extras: a check mark and scans coming in.
        if (chips) {
            tl.from(part('chip'), { scale: 0.6, opacity: 0, ...center, duration: 0.7, stagger: 0.3, ease: 'back.out(2)' }, 5.5).from(
                part('bar'),
                { scaleY: 0, transformOrigin: '50% 100%', duration: 0.6, stagger: 0.1 },
                5.9,
            );
        }

        return tl;
    }

    const media = gsap.matchMedia();

    // Large screens: pin the hero and let the scroll position drive the story.
    media.add('(min-width: 1024px)', () => {
        ScrollTrigger.create({
            trigger: hero,
            start: () => `top ${header?.offsetHeight ?? 0}px`,
            end: '+=110%',
            pin: true,
            scrub: 0.6,
            anticipatePin: 1,
            invalidateOnRefresh: true,
            animation: timeline(),
        });
    });

    // Small screens: no pinning (scroll-jacking on a phone is hostile); a shorter version plays once.
    media.add('(max-width: 1023.98px)', () => {
        const tl = timeline({ particles: false }).pause().timeScale(2.2);
        ScrollTrigger.create({ trigger: scene, start: 'top 85%', once: true, onEnter: () => tl.play() });
    });

    // The scene was hidden until now (see app.css) so the finished frame never flashes first.
    scene.dataset.ready = '';
    gsap.from(scene, { opacity: 0, duration: 0.4 });
}
