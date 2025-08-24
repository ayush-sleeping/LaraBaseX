import { ImgHTMLAttributes } from 'react';

export default function AppLogoIcon(props: ImgHTMLAttributes<HTMLImageElement>) {
    return <img src="/logo_larabasex.png" alt="LaraBaseX Logo" width={40} height={42} {...props} />;
}
