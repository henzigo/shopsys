import { Heading } from 'components/Basic/Heading/Heading';
import { IconImage } from 'components/Basic/Icon/IconImage';
import { InstagramIcon, YoutubeIcon } from 'components/Basic/Icon/IconsSvg';
import useTranslation from 'next-translate/useTranslation';

export const FooterContact: FC = () => {
    const { t } = useTranslation();

    return (
        <>
            <Heading className="text-center uppercase text-white" type="h4">
                {t('Follow Us')}
            </Heading>

            <div className="flex h-24 w-full max-w-xs overflow-hidden rounded border-2 border-greyLight">
                <FooterContactSocialsItem href="#" title="Instagram">
                    <InstagramIcon className="w-8 text-white" />
                </FooterContactSocialsItem>
                <FooterContactSocialsItem href="#" title="Facebook">
                    <IconImage alt={t('Facebook')} className="w-8" icon="facebook" />
                </FooterContactSocialsItem>
                <FooterContactSocialsItem href="#" title="Youtube">
                    <YoutubeIcon className="w-11 text-[#d93738]" />
                </FooterContactSocialsItem>
            </div>

            <div className="mt-4 flex flex-wrap justify-center gap-5">
                <FooterContactLangsItem href="#" text={t('Czechia')}>
                    <IconImage alt={t('Czechia')} height={16} icon="cz" width={24} />
                </FooterContactLangsItem>
                <FooterContactLangsItem href="#" text={t('Slovakia')}>
                    <IconImage alt={t('Slovakia')} height={16} icon="sk" width={24} />
                </FooterContactLangsItem>
            </div>
        </>
    );
};

const FooterContactSocialsItem: FC<{ href: string; title: string }> = ({ children, title, href }) => (
    <a className="flex h-full w-1/3 items-center justify-center first:border-none" href={href} title={title}>
        {children}
    </a>
);

const FooterContactLangsItem: FC<{ href: string; text: string }> = ({ children, href, text }) => (
    <a className="flex items-center hover:text-greyLight hover:no-underline" href={href}>
        {children}
        <span className="ml-2 text-sm text-greyLight">{text}</span>
    </a>
);
