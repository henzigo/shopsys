import { CrossIcon } from 'components/Basic/Icon/CrossIcon';
import { FieldError } from 'react-hook-form';
import { twJoin } from 'tailwind-merge';

type FormLineErrorProps = {
    inputType: 'textarea' | 'text-input' | 'checkbox' | 'text-input-password' | 'select' | 'dropzone';
    textInputSize?: 'small' | 'default';
    error?: FieldError;
};

export const FormLineError: FC<FormLineErrorProps> = ({ inputType, error, textInputSize }) => {
    if (error === undefined) {
        return null;
    }

    const isInputOrTextArea = inputType === 'text-input' || inputType === 'textarea';
    const isInputPassword = inputType === 'text-input-password';
    const isCheckbox = inputType === 'checkbox';
    const isSelect = inputType === 'select';
    const isDropzone = inputType === 'dropzone';

    const isTextInputSmall = textInputSize === 'small';

    return (
        <div className="relative mt-2">
            <CrossIcon
                className={twJoin(
                    'absolute flex w-4 text-inputError',
                    isInputOrTextArea && `right-5 -translate-y-1/2 ${isTextInputSmall ? '-top-8' : '-top-9'}`,
                    isInputPassword && `right-11 -translate-y-1/2 ${isTextInputSmall ? '-top-8' : '-top-9'}`,
                    isCheckbox && 'right-1 top-1/2 -translate-y-1/2',
                    isSelect && '-top-10 right-11 z-[2]',
                    isDropzone && 'right-0 top-1',
                )}
            />
            {error.message !== undefined && (
                <span className={twJoin('font-secondary text-sm text-inputError', isCheckbox && 'block pr-6')}>
                    {error.message}
                </span>
            )}
        </div>
    );
};
